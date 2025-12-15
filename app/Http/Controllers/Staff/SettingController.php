<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return view('staff.settings.index', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'full_name' => 'required|string|max:255',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Tambahkan mimes agar lebih spesifik
        ]);

        if ($request->hasFile('avatar')) {
            try {
                // 1. Simpan ke folder 'profiles' di disk 'supabase'
                // Ini akan masuk ke bucket: chat-attachments/profiles/filename.jpg
                $path = $request->file('avatar')->store('profiles', 'supabase');

                // 2. Ambil URL Publik
                /** @var \Illuminate\Filesystem\FilesystemAdapter $filesystem */
                $filesystem = Storage::disk('supabase');
                $url = $filesystem->url($path);

                // 3. Simpan URL ke database
                $user->avatar_url = $url;
            } catch (\Exception $e) {
                return back()->withErrors(['avatar' => 'Gagal upload ke storage: ' . $e->getMessage()]);
            }
        }

        $user->full_name = $request->full_name;
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updateBank(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'bank_name' => 'required|string|max:100',
            'bank_account' => 'required|string|max:50',
            'bank_holder' => 'required|string|max:100',
        ]);

        $user->update([
            'bank_name' => $request->bank_name,
            'bank_account' => $request->bank_account,
            'bank_holder' => $request->bank_holder,
        ]);

        return back()->with('success', 'Detail pembayaran berhasil disimpan.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/',
        ], [
            'new_password.regex' => 'Password baru harus mengandung huruf besar, huruf kecil, dan angka.'
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak cocok.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Kata sandi berhasil diubah.');
    }
}
