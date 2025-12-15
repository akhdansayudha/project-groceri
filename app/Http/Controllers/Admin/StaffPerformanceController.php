<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Task;
use App\Models\StaffPayout;
use Illuminate\Support\Facades\DB;

class StaffPerformanceController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        // 1. AMBIL RATE PAYOUT SAAT INI
        $currentRate = DB::table('agency_settings')->where('id', 1)->value('payout_rate_per_token') ?? 10000;

        // 2. QUERY STAFF
        $staffQuery = User::where('role', 'staff')
            ->with(['wallet'])
            ->withCount(['tasks as active_tasks_count' => function ($q) {
                $q->whereIn('status', ['active', 'in_progress', 'review', 'revision']);
            }])
            ->withCount(['tasks as completed_tasks_count' => function ($q) {
                $q->where('status', 'completed');
            }])
            // Hitung Total Payout IDR
            ->withSum(['payouts as total_payout_idr' => function ($q) {
                $q->where('status', 'approved');
            }], 'amount_currency');

        if ($search) {
            $staffQuery->where(function($q) use ($search) {
                $q->where('full_name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%");
            });
        }

        // FIX: SORTING LEADERBOARD (PostgreSQL Friendly)
        // Kita gunakan orderByRaw dengan subquery yang sama persis agar PostgreSQL paham
        $staffs = $staffQuery->orderByRaw('
            (SELECT COALESCE(SUM(amount_currency), 0) 
             FROM staff_payouts 
             WHERE staff_payouts.user_id = users.id 
             AND status = \'approved\') DESC
        ')
        ->paginate(10, ['*'], 'staff_page')
        ->withQueryString();

        // 3. DATA PENDUKUNG LAINNYA
        $pendingPayouts = StaffPayout::where('status', 'pending')
            ->with('user.wallet')
            ->orderBy('created_at', 'asc')
            ->get();

        $payoutHistory = StaffPayout::where('status', 'approved')
            ->with('user')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        $stats = [
            'pending_tx' => StaffPayout::where('status', 'pending')->sum('amount_token'),
            'pending_count' => $pendingPayouts->count(),
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

    // ... method approvePayout tetap sama ...
    public function approvePayout(Request $request, $id)
    {
        $request->validate([
            'amount_idr' => 'required|numeric|min:0', // Admin input nominal Rupiah real
        ]);

        DB::transaction(function () use ($request, $id) {
            $payout = StaffPayout::findOrFail($id);

            // Cek apakah sudah diapprove sebelumnya untuk mencegah double deduct
            if ($payout->status != 'pending') {
                return;
            }

            $payout->update([
                'status' => 'approved',
                'amount_currency' => $request->amount_idr,
                'updated_at' => now()
            ]);

            // Kurangi Saldo Wallet Staff
            $wallet = $payout->user->wallet;
            $wallet->decrement('balance', $payout->amount_token);
        });

        return back()->with('success', 'Payout approved successfully.');
    }
}
