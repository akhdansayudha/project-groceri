<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login untuk Staff.
     * URL: /staff/login
     */
    public function index()
    {
        // Redirect jika sudah login dan memiliki role yang benar
        if (Auth::check() && Auth::user()->role === 'staff') {
            return redirect()->route('staff.dashboard');
        }

        return view('staff.auth.login');
    }

    /**
     * Memproses permintaan login Staff.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            // CEK ROLE KHUSUS: Hanya izinkan STAFF
            if (Auth::user()->role !== 'staff') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun ini tidak memiliki akses Staff Portal.',
                ]);
            }

            return redirect()->route('staff.dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ]);
    }

    /**
     * Logout Staff
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('staff.login');
    }
}
