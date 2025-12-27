<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class SettingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('client.settings.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $isGoogleUser = !empty($user->google_id);

        // 1. Setup Rules Dasar (Nama & Avatar selalu boleh diubah)
        $rules = [
            'full_name' => 'required|string|max:255',
            'avatar' => 'nullable|image|max:2048',
        ];

        // 2. Tambahkan Rules Email & Password HANYA jika BUKAN user Google
        if (!$isGoogleUser) {
            $rules['email'] = 'required|email|unique:users,email,' . $user->id;
            $rules['current_password'] = 'nullable|required_with:new_password';
            $rules['new_password'] = 'nullable|min:8|confirmed';
        }

        // Jalankan Validasi
        $request->validate($rules);

        // 3. Update Basic Info
        $user->full_name = $request->full_name;

        // Hanya update email jika bukan user Google
        if (!$isGoogleUser) {
            $user->email = $request->email;
        }

        // 4. Handle Avatar Upload (Sama seperti sebelumnya)
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('profiles', 'supabase');
            $user->avatar_url = $path;
        }

        // 5. Handle Password Update (Hanya jika bukan user Google)
        if (!$isGoogleUser && $request->filled('current_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini salah.']);
            }
            $user->password = Hash::make($request->new_password);
        }

        /** @var \App\Models\User $user */
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}
