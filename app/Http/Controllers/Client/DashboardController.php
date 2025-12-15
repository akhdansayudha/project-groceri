<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use App\Models\Notification; // Pastikan Model ini di-use
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $user->load(['wallet.tier', 'workspaces']);

        $wallet = $user->wallet;
        $tier = $wallet ? $wallet->tier : null;
        $maxSlots = $tier ? $tier->max_active_tasks : 1;

        // 1. Ambil Workspace User
        $workspaces = $user->workspaces()
            ->withCount('tasks')
            ->orderBy('updated_at', 'desc')
            ->limit(4)
            ->get();

        // 2. Ambil Task Aktif
        $activeTasks = Task::where('user_id', $user->id)
            ->whereIn('status', ['active', 'in_progress', 'review', 'revision'])
            ->with('service')
            ->orderBy('updated_at', 'desc')
            ->limit($maxSlots)
            ->get();

        // 3. Ambil Queue
        $queueTasks = Task::where('user_id', $user->id)
            ->where('status', 'queue')
            ->with('service')
            ->orderBy('created_at', 'asc')
            ->limit(5)
            ->get();

        // 4. Ambil Notifikasi
        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // --- BAGIAN INI YANG HILANG SEBELUMNYA ---
        // Hitung jumlah notifikasi yang belum dibaca
        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
        // ------------------------------------------

        return view('client.dashboard.index', compact(
            'user',
            'wallet',
            'tier',
            'workspaces',
            'maxSlots',
            'activeTasks',
            'queueTasks',
            'notifications',
            'unreadCount' // Sekarang variabel ini sudah ada isinya
        ));
    }
}
