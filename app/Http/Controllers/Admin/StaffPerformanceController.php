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
            // BARU: Total Payout Rupiah
            'total_payout_idr' => StaffPayout::where('user_id', $id)->where('status', 'approved')->sum('amount_currency')
        ];

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

        return view('admin.performance.show', compact('staff', 'stats', 'serviceStats', 'projects', 'payouts'));
    }
}
