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
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            try {
                // Hapus avatar lama jika bukan default (Opsional)
                // if ($user->avatar_url && !str_contains($user->avatar_url, 'ui-avatars.com')) { ... }

                // Simpan ke folder 'profiles' di bucket Supabase
                $path = $request->file('avatar')->store('profiles', 'supabase');

                /** @var \Illuminate\Filesystem\FilesystemAdapter $filesystem */
                $filesystem = Storage::disk('supabase');
                $url = $filesystem->url($path);

                $user->avatar_url = $url;
            } catch (\Exception $e) {
                return back()->withErrors(['avatar' => 'Gagal upload: ' . $e->getMessage()]);
            }
        }

        if ($request->filled('full_name')) {
            $user->full_name = $request->full_name;
        }

        $user->save();

        return back()->with('success', 'Public profile updated successfully.');
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

        return back()->with('success', 'Payment details saved successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/',
        ], [
            'new_password.regex' => 'Password must contain mixed case letters and numbers.'
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Incorrect current password.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Security password updated successfully.');
    }
}
