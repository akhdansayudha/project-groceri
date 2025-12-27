<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman Login / Register
     */
    public function index()
    {
        return view('auth.login');
    }

    /**
     * Menangani Proses Login Manual
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            // Jika user ada, cek apakah dia user Google (punya google_id)
            if (!empty($user->google_id)) {
                return back()->withErrors([
                    'email' => 'Akun ini terdaftar menggunakan Google. Silakan klik tombol "Sign in with Google".',
                ])->onlyInput('email');
            }
        }

        $remember = $request->has('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            return $this->redirectBasedOnRole(Auth::user());
        }

        return back()->withErrors([
            'email' => 'Kombinasi email dan password tidak ditemukan.',
        ])->onlyInput('email');
    }

    /**
     * Menangani Proses Register Manual
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Create User
        $user = User::create([
            'full_name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'client', // Default role
            'avatar_url' => 'https://ui-avatars.com/api/?name=' . urlencode($request->name),
        ]);

        try {
            $data = ['user' => $user];

            Mail::send('auth.emails.welcome', $data, function ($message) use ($user) {
                $message->to($user->email);
                $message->subject('Welcome to Vektora - Your Creative Journey Starts Here');
            });
        } catch (\Exception $e) {
            // Mencatat error ke file log (storage/logs/laravel.log)
            Log::error("Gagal mengirim Welcome Email ke {$user->email}. Error: " . $e->getMessage());
        }

        Auth::login($user);

        return $this->redirectBasedOnRole($user);
    }

    /**
     * Menangani Logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    /**
     * Helper: Redirect logic based on Role
     */
    protected function redirectBasedOnRole($user)
    {
        $role = $user->role ?? 'client';

        switch ($role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'staff':
                return redirect()->route('staff.dashboard');
            default:
                return redirect()->route('client.dashboard');
        }
    }
}
