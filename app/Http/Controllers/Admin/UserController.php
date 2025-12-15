<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Workspace;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Menampilkan Daftar Client
     */
    public function index(Request $request)
    {
        $search = $request->query('search');

        // 1. Query Dasar: Hanya ambil role 'client'
        $query = User::where('role', 'client')
            ->with(['wallet.tier']);

        // 2. Fitur Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        // 3. Pagination
        $clients = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // 4. LOGIC REALTIME ONLINE STATUS (FIXED: Hanya Role Client)
        // Kita join ke tabel users untuk memfilter role
        $onlineUserIds = DB::table('sessions')
            ->join('users', 'sessions.user_id', '=', 'users.id') // Join tabel users
            ->where('users.role', 'client') // Filter: HANYA CLIENT
            ->where('sessions.last_activity', '>=', now()->subMinutes(5)->timestamp)
            ->pluck('sessions.user_id')
            ->toArray();

        // 5. Statistik Ringkas Header
        $totalClients = User::where('role', 'client')->count();
        $totalOnline = count($onlineUserIds); // Sekarang total ini murni hanya jumlah client

        // Menghitung Total Token Beredar
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

        // Cek Online Status User ini
        $isOnline = DB::table('sessions')
            ->where('user_id', $user->id)
            ->where('last_activity', '>=', now()->subMinutes(5)->timestamp)
            ->exists();

        // Statistik Personal Client
        $stats = [
            'projects_total' => Task::where('user_id', $user->id)->count(),
            'projects_active' => Task::where('user_id', $user->id)->whereIn('status', ['active', 'in_progress'])->count(),
            'total_spent' => $user->wallet->total_purchased ?? 0, // Ambil dari history topup/usage
        ];

        // Riwayat Project Terakhir
        $recentProjects = Task::where('user_id', $user->id)
            ->with('service')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.users.show', compact('user', 'isOnline', 'stats', 'recentProjects'));
    }

    /**
     * Hapus Client (Hati-hati)
     */
    public function destroy($id)
    {
        // Fitur delete bisa ditambahkan nanti dengan SoftDeletes
        // Untuk sekarang kita disable atau buat simple delete
        $user = User::findOrFail($id);

        // Hapus data terkait (opsional, tergantung kebijakan constraint DB)
        $user->delete();

        return back()->with('error', 'Fitur hapus client dinonaktifkan demi keamanan data project.');
    }
}
