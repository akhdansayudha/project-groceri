<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class GoogleAuthController extends Controller
{
    /**
     * Redirect user ke halaman login Google
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle callback dari Google
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = User::where('email', $googleUser->getEmail())->first();

            // SKENARIO 1: User sudah ada (Logic Login seperti sebelumnya)
            if ($user) {
                if (empty($user->google_id)) {
                    return redirect()->route('login')->withErrors([
                        'email' => 'Email ini terdaftar menggunakan Email. Silakan login menggunakan form email & password.',
                    ]);
                }
                if ($user->google_id !== $googleUser->getId()) {
                    return redirect()->route('login')->withErrors([
                        'email' => 'Terjadi kesalahan validasi akun Google.',
                    ]);
                }
                Auth::login($user);
                return $this->redirectBasedOnRole($user);
            }

            // SKENARIO 2: User Belum Ada (Register Baru via Google)
            $newUser = User::create([
                'full_name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar_url' => $googleUser->getAvatar(),
                'password' => null,
                'role' => 'client',
                'created_at' => now(),
            ]);

            // --- PENGIRIMAN EMAIL DENGAN ERROR LOGGING LENGKAP ---
            try {
                $data = ['user' => $newUser];

                Mail::send('auth.emails.welcome', $data, function ($message) use ($newUser) {
                    $message->to($newUser->email);
                    $message->subject('Welcome to Vektora - Your Creative Journey Starts Here');
                });
            } catch (\Exception $e) {
                // Mencatat error ke file log
                Log::error("Gagal mengirim Welcome Email Google ke {$newUser->email}. Error: " . $e->getMessage());
            }

            Auth::login($newUser);
            return $this->redirectBasedOnRole($newUser);
        } catch (Exception $e) {
            // Log error utama (misal koneksi ke Google gagal)
            Log::error("Gagal Login/Register Google. Error: " . $e->getMessage());

            return redirect()->route('login')->withErrors([
                'email' => 'Gagal login dengan Google. Silakan coba lagi.',
            ]);
        }
    }

    /**
     * Helper Redirect (Sama seperti di AuthController)
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
