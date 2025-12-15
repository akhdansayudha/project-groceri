<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Service;
use App\Models\Task;
use App\Models\Transaction;
use App\Models\Workspace;
use App\Models\TaskMessage;

class RequestController extends Controller
{
    /**
     * Menampilkan daftar semua request (My Requests)
     */
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Query Dasar (Eager Load Service & Workspace biar ringan)
        $query = Task::where('user_id', $user->id)
            ->with(['service', 'workspace']);

        // 2. Fitur Pencarian (Search by Title)
        if ($request->has('search') && $request->search != '') {
            $query->where('title', 'ilike', '%' . $request->search . '%');
        }

        // 3. Fitur Filter by Workspace
        if ($request->has('workspace_id') && $request->workspace_id != 'all') {
            $query->where('workspace_id', $request->workspace_id);
        }

        // 4. Fitur Filter by Status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Ambil Data dengan Pagination (10 per halaman)
        $tasks = $query->orderBy('updated_at', 'desc')->paginate(10);

        // Ambil Data Pendukung untuk Filter & Stats
        $workspaces = $user->workspaces;

        // Hitung Statistik Sederhana untuk Header
        $stats = [
            'total' => Task::where('user_id', $user->id)->count(),
            'active' => Task::where('user_id', $user->id)->whereIn('status', ['active', 'in_progress', 'review', 'revision'])->count(),
            'completed' => Task::where('user_id', $user->id)->where('status', 'completed')->count(),
        ];

        return view('client.requests.index', compact('tasks', 'workspaces', 'stats'));
    }

    public function create(Request $request) // <--- Inject Request
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $workspaces = $user->workspaces;
        $services = \App\Models\Service::where('is_active', true)->get();
        $balance = $user->wallet->balance ?? 0;

        // Ambil ID dari URL (jika ada)
        $preselectedWorkspaceId = $request->query('workspace_id');

        return view('client.requests.create', compact('services', 'balance', 'workspaces', 'preselectedWorkspaceId'));
    }

    /**
     * Memproses Penyimpanan Request Baru
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'workspace_id' => 'required|exists:workspaces,id',
            'service_id' => 'required|exists:services,id',
            'title' => 'required|string|max:255',
            'deadline' => 'required|date|after:today',
            'brief_data' => 'nullable|array',
            // VALIDASI TAMBAHAN UNTUK FILE
            'attachments' => 'nullable|file|max:10240', // Maks 10MB
        ]);

        $user = Auth::user();
        $service = Service::find($request->service_id);

        // 2. Cek Saldo Cukup
        if ($user->wallet->balance < $service->toratix_cost) {
            return back()->with('error', 'Saldo Toratix tidak mencukupi. Silakan Top Up terlebih dahulu.');
        }

        try {
            DB::beginTransaction();

            // --- LOGIKA UPLOAD FILE (BARU) ---
            $attachmentData = null; // Default null jika tidak ada file

            if ($request->hasFile('attachments')) {
                $file = $request->file('attachments');

                // Ambil nama asli file & bersihkan karakter aneh
                $originalName = $file->getClientOriginalName();
                $cleanName = preg_replace('/[^A-Za-z0-9.\-_]/', '', $originalName);

                // Tambahkan Timestamp agar nama file unik (menghindari replace file lama)
                $fileNameToStore = time() . '_' . $cleanName;

                // Upload ke Supabase (Folder 'tasks')
                // Pastikan disk 'supabase' sudah dikonfigurasi di config/filesystems.php
                $path = $file->storeAs('tasks', $fileNameToStore, 'supabase');

                // Siapkan data JSON untuk disimpan ke kolom 'attachments'
                $attachmentData = [
                    'path' => $path,
                    'original_name' => $originalName,
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                    'uploaded_at' => now()->toDateTimeString()
                ];
            }
            // ---------------------------------

            // 3. Simpan Task Baru
            $task = Task::create([
                'user_id' => $user->id,
                'workspace_id' => $request->workspace_id,
                'service_id' => $service->id,
                'title' => $request->title,
                'description' => $request->description,
                'deadline' => $request->deadline,
                'brief_data' => $request->brief_data,
                'status' => 'queue',
                'toratix_locked' => $service->toratix_cost,
                // SIMPAN DATA ATTACHMENT KE DATABASE
                'attachments' => $attachmentData,
            ]);

            // 4. Kurangi Saldo User
            $user->wallet->decrement('balance', $service->toratix_cost);

            // 5. Catat History Transaksi
            Transaction::create([
                'wallet_id' => $user->wallet->id,
                'type' => 'usage',
                'amount' => $service->toratix_cost,
                'description' => 'Project Request: ' . $request->title,
                'reference_id' => $task->id
            ]);

            DB::commit();

            return redirect()->route('client.dashboard')->with('success', 'Project berhasil dibuat dan masuk antrian!');
        } catch (\Exception $e) {
            DB::rollBack();
            // Hapus file jika database gagal disimpan (opsional, untuk kebersihan storage)
            // if (isset($path)) Storage::disk('supabase')->delete($path);

            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan Detail Project
     */
    public function show($id)
    {
        $task = Task::where('user_id', Auth::id())
            ->with(['service', 'workspace', 'messages']) // Eager load messages jika ada chat
            ->findOrFail($id);

        return view('client.requests.show', compact('task'));
    }

    /**
     * Menampilkan Form Edit (Hanya jika status Queue)
     */
    public function edit($id)
    {
        $task = Task::where('user_id', Auth::id())->findOrFail($id);

        if ($task->status !== 'queue') {
            return back()->with('error', 'Project yang sudah berjalan tidak dapat diedit.');
        }

        $workspaces = Auth::user()->workspaces;
        // Service tidak bisa diganti karena menyangkut harga/token

        return view('client.requests.edit', compact('task', 'workspaces'));
    }

    /**
     * Update Project (Hanya jika status Queue)
     */
    public function update(Request $request, $id)
    {
        $task = Task::where('user_id', Auth::id())->findOrFail($id);

        if ($task->status !== 'queue') {
            return back()->with('error', 'Project tidak dapat diubah karena sudah diproses.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'deadline' => 'required|date|after:today',
        ]);

        $task->update([
            'title' => $request->title,
            'description' => $request->description,
            'deadline' => $request->deadline,
            // Tambahkan field lain jika perlu (brief_data update logic)
        ]);

        return redirect()->route('client.requests.show', $task->id)
            ->with('success', 'Detail project berhasil diperbarui.');
    }

    /**
     * Menghapus Project & Refund Token (Hanya jika status Queue)
     */
    public function destroy($id)
    {
        $task = Task::where('user_id', Auth::id())->findOrFail($id);

        // 1. Cek Status
        if ($task->status !== 'queue') {
            return back()->with('error', 'Project ini sedang dikerjakan dan tidak dapat dibatalkan.');
        }

        try {
            DB::beginTransaction();

            $user = Auth::user();
            $refundAmount = $task->toratix_locked;

            // 2. Refund Saldo ke Wallet
            if ($refundAmount > 0) {
                $user->wallet->increment('balance', $refundAmount);

                // Opsional: kurangi total_purchased jika refund dianggap membatalkan pembelian 
                // (biasanya tidak, karena total_purchased adalah sejarah beli token, bukan pemakaian)
                // $user->wallet->decrement('total_purchased', $refundAmount); 
            }

            // 3. Catat Transaksi Refund
            Transaction::create([
                'wallet_id' => $user->wallet->id,
                'type' => 'refund', // Buat tipe baru 'refund' atau pakai 'credit'
                'amount' => $refundAmount,
                'description' => 'Refund from cancelled project: ' . $task->title,
                'reference_id' => $task->id
            ]);

            // 4. Hapus Task (Atau Soft Delete jika pakai Trait SoftDeletes)
            $task->delete();

            DB::commit();

            return redirect()->route('client.requests.index')
                ->with('success', 'Project dibatalkan. ' . $refundAmount . ' Token telah dikembalikan ke wallet Anda.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membatalkan project: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan Halaman Chat Project
     */
    public function chat($id)
    {
        $task = Task::where('user_id', Auth::id())
            ->with(['service', 'messages.user']) // Load pesan beserta pengirimnya
            ->findOrFail($id);

        // Validasi: Chat hanya boleh jika status BUKAN queue
        if ($task->status === 'queue') {
            return redirect()->route('client.requests.show', $id)
                ->with('error', 'Fitur chat belum tersedia saat status masih Queue.');
        }

        return view('client.requests.chat', compact('task'));
    }

    /**
     * Memproses Pengiriman Pesan Chat
     */
    public function chatStore(Request $request, $id)
    {
        $task = Task::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'message' => 'nullable|string',
            'attachment' => 'nullable|file|max:5120',
        ]);

        if (!$request->message && !$request->hasFile('attachment')) {
            return back()->with('error', 'Pesan tidak boleh kosong.');
        }

        try {
            DB::beginTransaction();

            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                // UPDATE DISINI: Gunakan disk 'supabase'
                // File akan tersimpan di bucket 'chat-attachments' di Supabase
                $attachmentPath = $request->file('attachment')->store('', 'supabase');
            }

            TaskMessage::create([
                'task_id' => $task->id,
                'sender_id' => Auth::id(),
                'content' => $request->message,
                'attachment_url' => $attachmentPath, // Simpan path-nya saja (misal: asd123hash.jpg)
                'is_read' => false,
                'created_at' => now(),
            ]);

            $task->touch();
            DB::commit();
            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }
}
