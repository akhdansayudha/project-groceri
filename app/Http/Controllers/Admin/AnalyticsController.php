<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Task;
use App\Models\User;
use App\Models\Service;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        // 1. DATA REVENUE (6 Bulan Terakhir)
        // Format label: ['Jan', 'Feb', 'Mar'...]
        // Format data: [1000000, 2500000, ...]
        $revenueData = Invoice::select(
            DB::raw('SUM(amount) as total'),
            DB::raw("TO_CHAR(created_at, 'Mon') as month_name"),
            DB::raw("EXTRACT(MONTH FROM created_at) as month_num")
        )
            ->where('status', 'paid')
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month_name', 'month_num')
            ->orderBy('month_num')
            ->get();

        $chartRevenue = [
            'labels' => $revenueData->pluck('month_name')->toArray(),
            'data' => $revenueData->pluck('total')->toArray()
        ];

        // 2. DATA LAYANAN TERLARIS (Top 5 Services)
        $topServices = Service::withCount('tasks')
            ->orderBy('tasks_count', 'desc')
            ->take(5)
            ->get();

        $chartServices = [
            'labels' => $topServices->pluck('name')->toArray(),
            'data' => $topServices->pluck('tasks_count')->toArray()
        ];

        // 3. PROJECT STATUS DISTRIBUTION
        $statusStats = Task::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Mapping status agar sesuai urutan warna di chart
        $chartStatus = [
            $statusStats['active'] ?? 0,
            $statusStats['queue'] ?? 0,
            $statusStats['completed'] ?? 0,
            $statusStats['revision'] ?? 0
        ];

        // 4. TOP SPENDER CLIENTS
        // Kita hitung manual user yang punya total payment invoice tertinggi
        $topClients = User::where('role', 'client')
            ->with(['wallet']) // Asumsi wallet menyimpan total pengeluaran/topup
            ->get()
            ->sortByDesc(function ($user) {
                // Logic sederhana: user dengan balance invoice paid terbanyak
                // Karena belum ada relasi user->invoices yg strict, kita ambil sample dummy atau query join
                // Disini kita pakai query Invoice join User untuk akurasi
                return Invoice::where('user_id', $user->id)->where('status', 'paid')->sum('amount');
            })
            ->take(5);

        // STATISTIK RINGKAS
        $summary = [
            'total_revenue' => Invoice::where('status', 'paid')->sum('amount'),
            'total_projects' => Task::count(),
            'avg_deal' => Invoice::where('status', 'paid')->avg('amount') ?? 0,
            'growth' => 12.5 // Hardcoded dummy growth %, nanti bisa dihitung real vs bulan lalu
        ];

        return view('admin.analytics.index', compact(
            'chartRevenue',
            'chartServices',
            'chartStatus',
            'topClients',
            'summary'
        ));
    }
}
