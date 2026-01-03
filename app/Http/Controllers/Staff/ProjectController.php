<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use App\Models\Deliverable;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Events\MessageSent;
use Illuminate\Support\Facades\View;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Ambil parameter filter
        $status = $request->query('status');
        $search = $request->query('search');
        $sort = $request->query('sort', 'newest'); // Default newest

        // 1. Query Dasar
        $query = Task::where('assignee_id', $user->id)
            ->with(['user', 'service', 'workspace']);

        // 2. Filter Status
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        } else {
            // Default: Tampilkan semua KECUALI queue (karena queue belum diambil staff)
            // Jika user klik "Completed", status akan terkirim 'completed' dan masuk logic IF di atas.
            // Jika 'all', kita tampilkan active s/d completed.
            $query->whereIn('status', ['active', 'in_progress', 'revision', 'review']);
        }

        // 3. Filter Search (Title atau Project ID/UUID)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', '%' . $search . '%')
                    ->orWhere('id', 'ilike', '%' . $search . '%');
                // Note: 'ilike' untuk PostgreSQL (case insensitive). Pakai 'like' jika MySQL.
            });
        }

        // 4. Sorting
        if ($sort == 'deadline') {
            $query->orderBy('deadline', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $tasks = $query->get();

        // 5. Hitung Counts (Tetap statis agar badge angka tidak berubah saat di-search)
        $baseCount = Task::where('assignee_id', $user->id);
        $counts = [
            'all' => (clone $baseCount)->whereIn('status', ['active', 'in_progress', 'revision', 'review'])->count(),
            'active' => (clone $baseCount)->where('status', 'active')->count(),
            'in_progress' => (clone $baseCount)->where('status', 'in_progress')->count(),
            'revision' => (clone $baseCount)->where('status', 'revision')->count(),
            'review' => (clone $baseCount)->where('status', 'review')->count(),
            // 'completed' => (clone $baseCount)->where('status', 'completed')->count(),
        ];

        return view('staff.projects.index', compact('tasks', 'counts', 'status', 'search', 'sort'));
    }

    public function show($id)
    {
        // Load deliverables untuk ditampilkan di history
        $task = Task::where('assignee_id', Auth::id())
            ->with(['user', 'service', 'workspace', 'deliverables'])
            ->findOrFail($id);

        // --- LOGIC CLIENT ONLINE STATUS ---
        // Cek apakah client aktif dalam 5 menit terakhir
        $onlineThreshold = now()->subMinutes(5)->timestamp;
        $isClientOnline = DB::table('sessions')
            ->where('user_id', $task->user_id)
            ->where('last_activity', '>=', $onlineThreshold)
            ->exists();

        return view('staff.projects.show', compact('task', 'isClientOnline'));
    }

    public function submit(Request $request, $id)
    {
        $task = Task::where('assignee_id', Auth::id())->findOrFail($id);
        $user = Auth::user();

        // Validasi input
        $request->validate([
            'submission_file' => 'required_without:submission_link|file|max:20480',
            'submission_link' => 'required_without:submission_file|nullable|url',
            'message' => 'nullable|string'
        ]);

        $url = null;
        $type = 'link';

        // 1. Handle File Upload
        if ($request->hasFile('submission_file')) {
            $path = $request->file('submission_file')->store('deliverables', 'supabase');
            $url = $path;
            $type = 'file';
        } else if ($request->filled('submission_link')) {
            $url = $request->submission_link;
            $type = 'link';
        }

        try {
            DB::beginTransaction();

            // 2. Simpan Deliverable (Ini akan membuat entry baru di history V1, V2, dst)
            Deliverable::create([
                'task_id' => $task->id,
                'staff_id' => $user->id,
                'file_url' => $url,
                'file_type' => $type,
                'message' => $request->message,
            ]);

            // 3. LOGIC STATUS & PAYMENT
            // Cek: Apakah ini submit revisi? (Status sebelumnya adalah 'revision')
            if ($task->status === 'revision') {

                // A. Auto Complete Project
                $task->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    // Kita anggap review sudah pass karena ini final fix
                ]);

                // B. Transfer Token ke Wallet Staff
                $amount = $task->toratix_locked;
                if ($amount > 0) {
                    // Tambah Saldo Staff
                    $user->wallet->increment('balance', $amount);

                    // Catat Transaksi Masuk (Earning)
                    Transaction::create([
                        'wallet_id' => $user->wallet->id,
                        'type' => 'earning', // Pastikan enum/type ini ada di DB Anda
                        'amount' => $amount,
                        'description' => "Payment for Project #{$task->id} (Auto-Completed)",
                        'reference_id' => $task->id
                    ]);
                }

                $message = 'Revision submitted. Project is automatically marked as Completed and payment released!';
            } else {
                // Flow Normal (Submit Pertama) -> Masuk ke Review
                $task->update([
                    'status' => 'review',
                    'review_at' => now()
                ]);

                $message = 'Work submitted successfully! Project is now under review.';
            }

            DB::commit();
            return back()->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to submit: ' . $e->getMessage());
        }
    }

    public function startWork($id)
    {
        $task = Task::where('assignee_id', Auth::id())->findOrFail($id);

        // Validasi: Hanya bisa start jika statusnya 'active'
        if ($task->status !== 'active') {
            return back()->with('error', 'Project already started or not valid.');
        }

        $task->update([
            'status' => 'in_progress',
            'started_at' => now(), // Opsional: jika ada kolom ini di DB
        ]);

        return back()->with('success', 'Project started! Submission center is now open.');
    }

    public function chat($id)
    {
        $task = Task::where('assignee_id', Auth::id())
            ->with(['messages.user', 'user'])
            ->findOrFail($id);

        // --- LOGIC ONLINE STATUS (5 Menit Terakhir) ---
        $onlineThreshold = now()->subMinutes(5)->timestamp;

        // 1. Cek Client Online
        // Cari session milik user_id (Client) yang activity-nya baru saja update
        $isClientOnline = DB::table('sessions')
            ->where('user_id', $task->user_id)
            ->where('last_activity', '>=', $onlineThreshold)
            ->exists();

        // 2. Cek Admin Online
        // Cari session milik user mana saja yang role-nya 'admin'
        $isAdminOnline = DB::table('sessions')
            ->join('users', 'sessions.user_id', '=', 'users.id')
            ->where('users.role', 'admin')
            ->where('sessions.last_activity', '>=', $onlineThreshold)
            ->exists();

        return view('staff.projects.chat', compact('task', 'isClientOnline', 'isAdminOnline'));
    }

    public function chatStore(Request $request, $id)
    {
        $task = Task::where('assignee_id', Auth::id())->findOrFail($id);

        if ($task->status === 'completed') {
            return response()->json(['status' => 'error', 'message' => 'Project closed.'], 403);
        }

        $request->validate([
            'message' => 'nullable|string',
            'attachment' => 'nullable|file|max:10240',
        ]);

        if (!$request->message && !$request->hasFile('attachment')) {
            return response()->json(['status' => 'error', 'message' => 'Message cannot be empty.'], 422);
        }

        try {
            DB::beginTransaction();

            $attachmentUrl = null;
            if ($request->hasFile('attachment')) {
                $attachmentUrl = $request->file('attachment')->store('chat-attachments', 'supabase');
            }

            // Simpan Pesan
            $msg = $task->messages()->create([
                'sender_id' => Auth::id(),
                'content' => $request->message,
                'attachment_url' => $attachmentUrl,
                'is_read' => false,
            ]);

            DB::commit();

            // --- REALTIME BROADCAST LOGIC ---

            // 1. Render HTML untuk Penerima (Client/Admin) -> $isMe = false
            $htmlOthers = View::make('staff.projects.partials.chat-bubble', [
                'msg' => $msg,
                'isMe' => false
            ])->render();

            // 2. Broadcast ke channel (Kecuali pengirim)
            broadcast(new MessageSent($msg, $htmlOthers))->toOthers();

            // 3. Render HTML untuk Pengirim (Staff sendiri) -> $isMe = true
            $htmlMe = View::make('staff.projects.partials.chat-bubble', [
                'msg' => $msg,
                'isMe' => true
            ])->render();

            return response()->json([
                'status' => 'success',
                'html' => $htmlMe
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Menampilkan Halaman Project History (Merged)
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $search = $request->query('search');

        // 1. Base Query (Status Completed)
        $query = Task::where('assignee_id', $user->id)
            ->where('status', 'completed')
            ->with(['user', 'service', 'workspace']);

        // 2. Filter Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'ilike', '%' . $search . '%')
                    ->orWhere('id', 'ilike', '%' . $search . '%');
            });
        }

        // 3. Get Data (Paginate)
        $tasks = $query->orderBy('completed_at', 'desc')->paginate(15);

        // --- STATISTIK ---

        // A. Total Earned (Lifetime)
        $totalEarned = Task::where('assignee_id', $user->id)
            ->where('status', 'completed')
            ->sum('toratix_locked');

        // B. Total Projects Completed
        $totalCompleted = Task::where('assignee_id', $user->id)
            ->where('status', 'completed')
            ->count();

        // C. Top Service (Layanan paling sering dikerjakan)
        $topServiceRow = Task::where('assignee_id', $user->id)
            ->where('status', 'completed')
            ->select('service_id', DB::raw('count(*) as total'))
            ->groupBy('service_id')
            ->orderByDesc('total')
            ->with('service')
            ->first();

        $topServiceName = $topServiceRow ? $topServiceRow->service->name : '-';

        return view('staff.projects.history', compact('tasks', 'search', 'totalEarned', 'totalCompleted', 'topServiceName'));
    }
}
