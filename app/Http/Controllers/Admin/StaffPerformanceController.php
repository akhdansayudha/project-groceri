<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Task;
use App\Models\Transaction;
use App\Models\StaffPayout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StaffPerformanceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $currentRate = DB::table('agency_settings')->where('id', 1)->value('payout_rate_per_token') ?? 10000;

        // 1. QUERY STAFF (LEADERBOARD) - FIX COUNT
        $staffQuery = User::where('role', 'staff')
            ->with(['wallet'])
            // FIX: Gunakan addSelect subquery agar menghitung berdasarkan 'assignee_id', bukan 'user_id'
            ->addSelect([
                'completed_tasks_count' => Task::selectRaw('count(*)')
                    ->whereColumn('assignee_id', 'users.id')
                    ->where('status', 'completed')
            ])
            // Total Payout Approved
            ->withSum(['payouts as total_payout_idr' => function ($q) {
                $q->where('status', 'approved');
            }], 'amount_currency');

        if ($search) {
            $staffQuery->where(function ($q) use ($search) {
                $q->where('full_name', 'ilike', "%{$search}%")
                    ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        $staffs = $staffQuery->orderByDesc('completed_tasks_count') // Sort by productivity
            ->paginate(10, ['*'], 'staff_page')
            ->withQueryString();

        // 2. PENDING PAYOUTS (Pagination 10)
        $pendingPayouts = StaffPayout::where('status', 'pending')
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->paginate(10, ['*'], 'pending_page');

        // 3. PAYOUT HISTORY (Pagination 10)
        $payoutHistory = StaffPayout::whereIn('status', ['approved', 'rejected']) // Ambil Approved & Rejected
            ->with('user')
            ->orderBy('updated_at', 'desc')
            ->paginate(10, ['*'], 'history_page');

        $stats = [
            'pending_tx' => StaffPayout::where('status', 'pending')->sum('amount_token'),
            'pending_count' => StaffPayout::where('status', 'pending')->count(), // Fix count tanpa pagination
            'total_paid_idr' => StaffPayout::where('status', 'approved')->sum('amount_currency'),
            'current_rate' => $currentRate,
        ];

        return view('admin.performance.index', compact('staffs', 'pendingPayouts', 'payoutHistory', 'stats', 'search'));
    }

    // METHOD BARU: UPDATE RATE
    public function updateRate(Request $request)
    {
        $request->validate([
            'rate_per_token' => 'required|integer|min:1'
        ]);

        // Update atau Insert jika belum ada (row ID 1)
        DB::table('agency_settings')->updateOrInsert(
            ['id' => 1],
            ['payout_rate_per_token' => $request->rate_per_token, 'updated_at' => now()]
        );

        return back()->with('success', 'Staff payout rate updated successfully.');
    }

    // APPROVE PAYOUT (WITH PROOF UPLOAD)
    public function approvePayout(Request $request, $id)
    {
        $request->validate([
            'proof_file' => 'required|file|image|max:2048',
            'admin_note' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request, $id) {
            $payout = StaffPayout::findOrFail($id);

            // Validasi status
            if ($payout->status != 'pending') return;

            // 1. Upload Bukti ke folder 'payouts'
            // Defaultnya return path relatif: "payouts/filename.jpg"
            $path = $request->file('proof_file')->store('payouts', 'supabase');

            // 2. Update Status Payout (FIXED)
            $payout->update([
                'status' => 'approved', // <--- UBAH DARI 'rejected' KE 'approved' / 'paid'
                'proof_url' => $path,   // Path ini akan tersimpan
                'admin_note' => $request->admin_note,
                'updated_at' => now()
            ]);
        });

        return back()->with('success', 'Payout approved & proof uploaded.');
    }

    // REJECT PAYOUT
    public function rejectPayout(Request $request, $id)
    {
        $request->validate(['reject_reason' => 'required|string']);

        DB::transaction(function () use ($request, $id) {
            // Load payout beserta relasi user dan wallet-nya
            $payout = StaffPayout::with('user.wallet')->findOrFail($id);

            if ($payout->status != 'pending') return;

            // 1. Update Status Payout jadi Rejected
            $payout->update([
                'status' => 'rejected',
                'admin_note' => $request->reject_reason,
                'updated_at' => now()
            ]);

            // 2. REFUND TOKEN KE WALLET STAFF
            $payout->user->wallet->increment('balance', $payout->amount_token);

            // 3. CATAT DI HISTORY TRANSAKSI (FIX)
            // Ini agar muncul di Wallet Activity halaman staff
            Transaction::create([
                'wallet_id' => $payout->user->wallet->id,
                'type' => 'refund', // Tipe transaksi refund
                'amount' => $payout->amount_token, // Nilai positif karena uang masuk kembali
                'description' => "Refund: Rejected Payout #PY-{$payout->id}",
                'reference_id' => null, // Null karena ID Payout pakai BigInt, reference_id biasanya UUID
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return back()->with('success', 'Payout rejected. Tokens refunded to staff.');
    }

    public function show($id)
    {
        $staff = User::with('wallet')->findOrFail($id);

        // 1. Statistik Utama
        $stats = [
            'active_tasks' => Task::where('assignee_id', $id)
                ->whereIn('status', ['active', 'in_progress', 'review', 'revision'])->count(),
            'completed_tasks' => Task::where('assignee_id', $id)
                ->where('status', 'completed')->count(),
            'total_earned' => $staff->wallet->total_earned ?? 0,
            'current_balance' => $staff->wallet->balance ?? 0,
            'total_payout_idr' => StaffPayout::where('user_id', $id)->where('status', 'approved')->sum('amount_currency'),
            'total_payout_tokens' => StaffPayout::where('user_id', $id)->where('status', 'approved')->sum('amount_token')
        ];

        // --- TAMBAHAN LOGIKA PERFORMANCE GRADE ---
        // Ambil semua task completed untuk kalkulasi akurat
        $completedTasksAll = Task::where('assignee_id', $id)
            ->where('status', 'completed')
            ->get();

        $totalCompleted = $completedTasksAll->count();

        // Hitung yang on-time
        $onTimeCount = $completedTasksAll->filter(function ($task) {
            return $task->deadline && $task->completed_at <= $task->deadline;
        })->count();

        // Hitung persentase
        $onTimeRate = $totalCompleted > 0 ? round(($onTimeCount / $totalCompleted) * 100) : 0;

        // 2. BARU: Statistik Breakdown Service (Jenis Project)
        // Mengambil project completed, dikelompokkan per nama service, dihitung jumlahnya
        $serviceStats = Task::where('assignee_id', $id)
            ->where('status', 'completed')
            ->join('services', 'tasks.service_id', '=', 'services.id')
            ->select('services.name', 'services.icon_url', DB::raw('count(*) as total'))
            ->groupBy('services.name', 'services.icon_url')
            ->orderByDesc('total')
            ->get();

        // 3. List Project
        $projects = Task::where('assignee_id', $id)
            ->with(['service', 'user'])
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        // 4. Riwayat Pembayaran
        $payouts = StaffPayout::where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        $currentRate = DB::table('agency_settings')->where('id', 1)->value('payout_rate_per_token') ?? 10000;

        return view('admin.performance.show', compact('staff', 'stats', 'serviceStats', 'projects', 'payouts', 'currentRate', 'onTimeRate'));
    }

    // --- METHOD BARU: ADMIN MANUAL PAYOUT ---
    public function storeManualPayout(Request $request, $id)
    {
        $request->validate([
            'amount_token' => 'required|integer|min:1',
            'proof_file'   => 'nullable|file|image|max:2048',
            'admin_note'   => 'nullable|string'
        ]);

        DB::transaction(function () use ($request, $id) {
            $staff = User::with('wallet')->findOrFail($id);

            // Validasi Saldo
            if ($staff->wallet->balance < $request->amount_token) {
                throw new \Exception("Saldo staff tidak mencukupi.");
            }

            // 1. Hitung Rupiah
            $rate = DB::table('agency_settings')->where('id', 1)->value('payout_rate_per_token') ?? 10000;
            $amountIdr = $request->amount_token * $rate;

            // 2. Upload Bukti (Jika ada)
            $proofPath = null;
            if ($request->hasFile('proof_file')) {
                $proofPath = $request->file('proof_file')->store('payouts', 'supabase');
            }

            // 3. Buat Record Payout & SIMPAN KE VARIABEL $payout
            $payout = StaffPayout::create([
                'user_id' => $staff->id,
                'amount_token' => $request->amount_token,
                'amount_currency' => $amountIdr,
                'status' => 'approved',
                'proof_url' => $proofPath,
                'bank_name' => $staff->bank_name,
                'bank_account' => $staff->bank_account,
                'bank_holder' => $staff->bank_holder,
                'admin_note' => $request->admin_note ?? 'Manual payout by Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 4. Potong Saldo Wallet
            $staff->wallet->decrement('balance', $request->amount_token);

            // 5. Catat Transaksi Wallet (UPDATE DESCRIPTION DISINI)
            Transaction::create([
                'wallet_id' => $staff->wallet->id,
                'type' => 'payout',
                'amount' => -$request->amount_token, // Minus
                // Menambahkan ID Payout ke deskripsi:
                'description' => "Payout Processed by Admin #PY-{$payout->id}",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return back()->with('success', 'Manual payout processed successfully.');
    }
}
