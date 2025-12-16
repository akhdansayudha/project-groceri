<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use App\Models\TaskMessage;
use App\Models\Transaction;
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
            'active' => Task::where('status', 'active')->count(),
            'in_progress' => Task::where('status', 'in_progress')->count(),
            'review' => Task::where('status', 'review')->count(),
            'revision' => Task::where('status', 'revision')->count(),
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
        $user = Auth::user(); // Admin yang sedang login

        // --- VALIDASI LIMIT PROJECT BERDASARKAN TIER (Start Project) ---
        if ($task->status == 'queue' && $request->status == 'active') {
            $userTier = $task->user->wallet->tier ?? null;
            if ($userTier) {
                $maxSlots = $userTier->max_active_tasks;
                $currentActiveCount = Task::where('user_id', $task->user_id)
                    ->whereIn('status', ['active', 'in_progress', 'review', 'revision'])
                    ->where('id', '!=', $id)
                    ->count();

                if ($currentActiveCount >= $maxSlots) {
                    return back()->with('error', "Limit active project client ({$maxSlots} slots) sudah penuh.");
                }
            }
        }
        // ---------------------------------------------------------------

        // --- LOGIC REVISI DENGAN CATATAN (Updated) ---
        if ($request->status == 'revision') {
            $request->validate([
                'revision_notes' => 'required|string'
            ]);

            DB::transaction(function () use ($task, $request, $user) {
                // 1. Update Status Project
                $task->update([
                    'status' => 'revision',
                    // Opsional: reset review_at atau set active_at jika perlu
                ]);

                // 2. Kirim Pesan Otomatis ke Chat Room
                TaskMessage::create([
                    'task_id' => $task->id,
                    'sender_id' => $user->id, // Admin ID
                    'content' => "REVISION REQUESTED (BY ADMIN):\n" . $request->revision_notes,
                    'is_read' => false,
                    'created_at' => now(),
                ]);
            });

            return back()->with('success', 'Revisi diminta. Catatan telah dikirim ke chat room.');
        }
        // ---------------------------------------------

        // Logic Update Status Biasa (Active, Completed, dll)
        $request->validate([
            'status' => 'required|string',
            'assignee_id' => 'nullable|exists:users,id',
        ]);

        // 1. Set Active Time (Saat Assign Staff)
        if ($task->status !== 'active' && $request->status == 'active') {
            $task->active_at = now();
        }

        // 2. Set Started Time (BARU: Saat Force Start / In Progress)
        // Ini akan mengisi kolom 'started_at' di tabel tasks
        if ($task->status !== 'in_progress' && $request->status == 'in_progress') {
            $task->started_at = now();
        }

        // 3. Set Completed Time (Saat Acc/Selesai)
        if ($request->status == 'completed' && $task->status != 'completed') {
            $task->completed_at = now();
            // Disini bisa ditambahkan logic transfer dana ke staff jika Admin yang complete
        }

        // Simpan Status Baru
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

        // 1. Cek Client Online
        $isClientOnline = DB::table('sessions')
            ->where('user_id', $task->user_id)
            ->where('last_activity', '>=', now()->subMinutes(5)->timestamp)
            ->exists();

        // 2. Cek Staff Online (Tambahan Baru)
        $isStaffOnline = false;
        if ($task->assignee_id) {
            $isStaffOnline = DB::table('sessions')
                ->where('user_id', $task->assignee_id)
                ->where('last_activity', '>=', now()->subMinutes(5)->timestamp)
                ->exists();
        }

        // Kirim variabel $isStaffOnline ke view
        return view('admin.projects.chat', compact('task', 'isClientOnline', 'isStaffOnline'));
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

    /**
     * DELETE PROJECT & REFUND TOKEN
     */
    public function destroy($id)
    {
        $task = Task::with(['user.wallet'])->findOrFail($id);

        if ($task->status !== 'queue') {
            return back()->with('error', 'Hanya project dengan status Queue yang bisa dihapus.');
        }

        DB::transaction(function () use ($task) {
            // 1. Refund Token ke Wallet Client
            if ($task->user && $task->user->wallet) {
                // A. Kembalikan Saldo
                $task->user->wallet->increment('balance', $task->toratix_locked);

                // B. CATAT TRANSAKSI HISTORY (FIX)
                // Ini agar muncul di Transaction History Client
                Transaction::create([
                    'wallet_id' => $task->user->wallet->id,
                    'type' => 'refund', // Tipe refund
                    'amount' => $task->toratix_locked, // Nilai positif (+)
                    'description' => 'Refund from cancelled project: ' . $task->title,
                    'reference_id' => $task->id, // ID Project sebagai referensi (meskipun dihapus, ID tetap tercatat di history)
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // 2. Hapus Project
            $task->delete();
        });

        return redirect()->route('admin.projects.index')->with('success', 'Project deleted and tokens refunded to client.');
    }

    /**
     * ADMIN SUBMIT WORK (GOD MODE)
     */
    public function submitWork(Request $request, $id)
    {
        $request->validate([
            'submission_file' => 'required_without:submission_link|file|max:10240', // Max 10MB
            'submission_link' => 'required_without:submission_file|nullable|url',
            'message' => 'nullable|string'
        ]);

        $task = Task::findOrFail($id);

        DB::transaction(function () use ($request, $task) {
            $filePath = null;
            $fileType = 'link';

            // Handle File Upload (Supabase)
            if ($request->hasFile('submission_file')) {
                $file = $request->file('submission_file');
                $filename = time() . '_' . $file->getClientOriginalName();
                // Simpan ke folder deliverables
                $filePath = $file->storeAs('deliverables', $filename, 'supabase');
                $fileType = 'file';
            } else {
                $filePath = $request->submission_link;
            }

            // Create Deliverable Record
            \App\Models\Deliverable::create([
                'task_id' => $task->id,
                'staff_id' => Auth::id(), // Admin dianggap sebagai pengerja
                'file_url' => $filePath,
                'file_type' => $fileType,
                'message' => $request->message,
                'created_at' => now(),
            ]);

            // Update Task Status -> Review
            $task->update([
                'status' => 'review',
                'review_at' => now()
            ]);
        });

        return back()->with('success', 'Work submitted for review successfully.');
    }
}
