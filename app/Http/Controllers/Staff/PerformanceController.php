<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PerformanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $range = $request->query('range', 'all_time'); // Default: all_time

        // 1. Setup Base Query (Hanya yang Completed)
        $query = Task::where('assignee_id', $user->id)
            ->where('status', 'completed');

        // 2. Terapkan Filter Waktu
        if ($range == 'last_month') {
            // Dari awal bulan lalu sampai akhir bulan lalu
            // Atau 30 hari terakhir? Biasanya "Last Month" di dashboard berarti 30 hari terakhir.
            // Kita pakai 30 hari terakhir agar datanya relevan.
            $startDate = now()->subDays(30);
            $query->where('completed_at', '>=', $startDate);
        }

        // Clone query untuk perhitungan agregat agar tidak reset
        $tasks = $query->get();

        // 3. Hitung Metrik Utama
        $totalCompleted = $tasks->count();
        $totalEarned = $tasks->sum('toratix_locked');

        // 4. Hitung On-Time Delivery Rate
        // Cek berapa task yang completed_at <= deadline
        $onTimeCount = $tasks->filter(function ($task) {
            return $task->deadline && $task->completed_at <= $task->deadline;
        })->count();

        $onTimeRate = $totalCompleted > 0 ? round(($onTimeCount / $totalCompleted) * 100) : 0;

        // 5. Service Breakdown (Analisis Per Layanan)
        // Kita kelompokkan berdasarkan service_id
        $serviceStats = $tasks->groupBy('service_id')->map(function ($group) {
            return [
                'service_name' => $group->first()->service->name,
                'count' => $group->count(),
                'earnings' => $group->sum('toratix_locked'),
            ];
        })->sortByDesc('earnings'); // Urutkan dari earning terbesar

        return view('staff.performance.index', compact(
            'range',
            'totalCompleted',
            'totalEarned',
            'onTimeRate',
            'serviceStats'
        ));
    }
}
