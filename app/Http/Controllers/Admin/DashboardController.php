<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Invoice;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Statistik Utama (Overview)
        $stats = [
            'revenue_month' => Invoice::where('status', 'paid')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
            'pending_projects' => Task::where('status', 'queue')->count(),
            'active_projects' => Task::whereIn('status', ['active', 'in_progress', 'review', 'revision'])->count(),
            'total_clients' => User::where('role', 'client')->count(),
        ];

        // 2. Project Terbaru (Queue)
        $recentProjects = Task::with(['user', 'service'])
            ->where('status', 'queue')
            ->orderBy('created_at', 'asc')
            ->take(5)
            ->get();

        // 3. Staff Availability (Realtime Online Status)
        $staffMembers = User::where('role', 'staff')
            ->select('users.*')
            // A. Cek Status Online (Subquery ke Sessions)
            ->addSelect([
                'is_currently_online' => DB::table('sessions')
                    ->selectRaw('count(*)')
                    ->whereColumn('user_id', 'users.id')
                    ->where('last_activity', '>=', now()->subMinutes(5)->timestamp)
                    ->limit(1)
            ])
            // B. Cek Waktu Logout Terakhir (Subquery ke Audit Logs)
            ->addSelect([
                'last_logout_at' => DB::table('audit_logs')
                    ->select('created_at')
                    ->whereColumn('user_id', 'users.id')
                    ->where('action', 'logout')
                    ->orderBy('created_at', 'desc')
                    ->limit(1)
            ])
            // Urutkan: Online duluan, baru berdasarkan login terakhir
            ->orderByDesc('is_currently_online')
            ->orderByDesc('last_login_at')
            ->take(5)
            ->get();

        // CATATAN: Loop foreach sebelumnya dihapus karena properti 'last_activity' tidak ada di select.
        // Kita sudah menggunakan 'is_currently_online' dari subquery di atas.

        return view('admin.dashboard.index', compact('stats', 'recentProjects', 'staffMembers'));
    }
}
