<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use App\Models\TaskMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    /**
     * Menampilkan daftar project dengan Filter
     */
    public function index(Request $request)
    {
        // Ambil status dari ?status=... (default: semua)
        $status = $request->query('status');

        $tasks = Task::with(['user', 'service', 'assignee', 'workspace'])
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('created_at', 'desc') // Project baru paling atas
            ->paginate(10)
            ->withQueryString();

        // Hitung Counter untuk Tab Navigasi
        $counts = [
            'all' => Task::count(),
            'queue' => Task::where('status', 'queue')->count(),
            'active' => Task::whereIn('status', ['active', 'in_progress'])->count(),
            'completed' => Task::where('status', 'completed')->count(),
        ];

        return view('admin.projects.index', compact('tasks', 'counts'));
    }

    /**
     * Menampilkan Detail Project
     */
    public function show($id)
    {
        $task = Task::with(['user.wallet.tier', 'service', 'assignee', 'workspace'])->findOrFail($id);

        // Staff untuk dropdown assignment
        $staffMembers = User::where('role', 'staff')->get();

        // LOGIC CHECK ONLINE STATUS
        $isUserOnline = DB::table('sessions')
            ->where('user_id', $task->user_id)
            ->where('last_activity', '>=', now()->subMinutes(5)->timestamp)
            ->exists();

        return view('admin.projects.show', compact('task', 'staffMembers', 'isUserOnline'));
    }

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        // --- VALIDASI LIMIT PROJECT BERDASARKAN TIER (BARU) ---
        // Jika status diubah dari 'queue' menjadi 'active' (Start Project)
        if ($task->status == 'queue' && $request->status == 'active') {

            // 1. Ambil Data Tier User
            // Pastikan relasi user -> wallet -> tier ada
            $userTier = $task->user->wallet->tier ?? null;

            if ($userTier) {
                $maxSlots = $userTier->max_active_tasks;

                // 2. Hitung Project User yang Sedang Berjalan (Active/In Progress/Review/Revision)
                $currentActiveCount = Task::where('user_id', $task->user_id)
                    ->whereIn('status', ['active', 'in_progress', 'review', 'revision'])
                    ->where('id', '!=', $id) // Kecualikan task ini sendiri
                    ->count();

                // 3. Cek Apakah Slot Penuh
                if ($currentActiveCount >= $maxSlots) {
                    return back()->with('error', "Gagal memulai project! Client ini (Tier: {$userTier->name}) hanya memiliki jatah {$maxSlots} slot active. Saat ini sudah ada {$currentActiveCount} project berjalan.");
                }
            }
        }
        // ------------------------------------------------------

        $request->validate([
            'status' => 'required|string',
            'assignee_id' => 'nullable|exists:users,id',
        ]);

        $task->status = $request->status;

        if ($request->has('assignee_id') && $request->assignee_id != null) {
            $task->assignee_id = $request->assignee_id;
        }

        $task->save();

        return back()->with('success', 'Status project berhasil diperbarui.');
    }

    // --- FITUR CHAT ADMIN ---
    public function chat($id)
    {
        $task = Task::with(['messages.user', 'user', 'assignee'])->findOrFail($id);

        $isClientOnline = DB::table('sessions')
            ->where('user_id', $task->user_id)
            ->where('last_activity', '>=', now()->subMinutes(5)->timestamp)
            ->exists();

        return view('admin.projects.chat', compact('task', 'isClientOnline'));
    }

    public function chatStore(Request $request, $id)
    {
        $request->validate(['message' => 'required|string']);

        TaskMessage::create([
            'task_id' => $id,
            'sender_id' => Auth::id(),
            'content' => $request->message,
            'is_read' => false,
            'attachment_url' => null
        ]);

        return back();
    }
}
