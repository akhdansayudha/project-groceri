<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use App\Models\StaffPayout;
use App\Models\Notification;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $staff */
        $staff = Auth::user();

        if ($staff->role !== 'staff') {
            abort(403, 'Akses Ditolak. Halaman ini khusus untuk Staff Vektora.');
        }

        // Eager load wallet untuk efisiensi query
        $staff->load('wallet');
        $staffId = $staff->id;

        // 1. Statistik Kinerja Pribadi
        $stats = [
            // Menghitung task yang sedang aktif dikerjakan
            'tasks_active' => Task::where('assignee_id', $staffId)
                ->whereIn('status', ['active', 'in_progress', 'review', 'revision'])
                ->count(),

            // Menghitung task yang sudah selesai
            'tasks_completed' => Task::where('assignee_id', $staffId)
                ->where('status', 'completed')
                ->count(),

            // Saldo Token (Toratix) saat ini
            'token_balance' => $staff->wallet->balance ?? 0,

            // Total pendapatan seumur hidup (jika ada field ini, opsional)
            'total_earned' => $staff->wallet->total_earned ?? 0,
        ];

        // 2. Daftar Tugas Terbaru (Untuk ditampilkan di tabel dashboard)
        $recentTasks = Task::where('assignee_id', $staffId)
            ->whereIn('status', ['active', 'in_progress', 'review', 'revision'])
            ->with(['user', 'service']) // Ambil data Client & Service terkait
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        // 3. Riwayat Pembayaran Terakhir
        $recentPayouts = StaffPayout::where('user_id', $staffId)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        $notifications = Notification::where('user_id', $staffId)
            ->orderBy('created_at', 'desc')
            ->take(10) // Batasi 10 notifikasi terbaru agar tidak berat
            ->get();

        $unreadCount = Notification::where('user_id', $staffId)
            ->where('is_read', false)
            ->count();

        return view('staff.dashboard.index', compact('stats', 'recentTasks', 'recentPayouts', 'notifications', 'unreadCount'));
    }
}
