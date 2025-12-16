<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage; // Pastikan import ini ada

class UserController extends Controller
{
    /**
     * Menampilkan Daftar Client
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        // 1. Query Dasar
        $query = User::where('role', 'client')->with(['wallet.tier']);

        // 2. Fitur Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        // 3. Pagination
        $clients = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // 4. LOGIC REALTIME ONLINE STATUS
        $onlineUserIds = DB::table('sessions')
            ->join('users', 'sessions.user_id', '=', 'users.id')
            ->where('users.role', 'client')
            ->where('sessions.last_activity', '>=', now()->subMinutes(5)->timestamp)
            ->pluck('sessions.user_id')
            ->toArray();

        // 5. Statistik
        $totalClients = User::where('role', 'client')->count();
        $totalOnline = count($onlineUserIds);
        $totalTokenCirculating = DB::table('users')
            ->join('wallets', 'users.id', '=', 'wallets.user_id')
            ->where('users.role', 'client')
            ->sum('wallets.balance');

        return view('admin.users.index', compact('clients', 'onlineUserIds', 'totalClients', 'totalOnline', 'totalTokenCirculating'));
    }

    /**
     * Menampilkan Detail Client
     */
    public function show($id)
    {
        $user = User::with(['wallet.tier', 'workspaces'])->findOrFail($id);

        $isOnline = DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('last_activity', '>=', now()->subMinutes(5)->timestamp)
            ->exists();

        $stats = [
            'projects_total' => Task::where('user_id', $user->id)->count(),
            'projects_active' => Task::where('user_id', $user->id)->whereIn('status', ['active', 'in_progress'])->count(),
            'total_spent' => $user->wallet->total_purchased ?? 0,
        ];

        $recentProjects = Task::where('user_id', $user->id)
            ->with('service')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.users.show', compact('user', 'isOnline', 'stats', 'recentProjects'));
    }

    /**
     * Update Client (Admin Editing)
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|max:2048', // Max 2MB
            'new_password' => 'nullable|min:8', // Password opsional
        ]);

        try {
            DB::beginTransaction();

            $user->full_name = $request->full_name;
            $user->email = $request->email;

            // Handle Password Change
            if ($request->filled('new_password')) {
                $user->password = Hash::make($request->new_password);
            }

            // Handle Avatar Upload (Supabase)
            if ($request->hasFile('avatar')) {
                // Upload file baru
                $path = $request->file('avatar')->store('profiles', 'supabase');
                $user->avatar_url = $path;
            }

            $user->save();

            DB::commit();
            return back()->with('success', 'Data client berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui client: ' . $e->getMessage());
        }
    }

    /**
     * Hapus Client
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Cek apakah punya project aktif
        $activeTasks = Task::where('user_id', $id)
            ->whereIn('status', ['queue', 'active', 'in_progress', 'review'])
            ->exists();

        if ($activeTasks) {
            return back()->with('error', 'Gagal menghapus! Client ini masih memiliki project aktif.');
        }

        try {
            // Hapus User (Cascade delete akan menangani data terkait jika disetting di DB)
            $user->delete();
            return back()->with('success', 'Client berhasil dihapus dari database.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
