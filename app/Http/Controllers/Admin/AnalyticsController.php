<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Task;
use App\Models\User;
use App\Models\Service;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Barryvdh\DomPDF\Facade\Pdf;

class AnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // 1. SETUP DATE RANGE
        if ($request->has('date_range') && !empty($request->date_range)) {
            $dates = explode(' to ', $request->date_range);
            $startDate = Carbon::parse($dates[0])->startOfDay();
            $endDate = isset($dates[1]) ? Carbon::parse($dates[1])->endOfDay() : $startDate->endOfDay();
        } else {
            $startDate = now()->subMonths(6)->startOfDay();
            $endDate = now()->endOfDay();
        }

        $selectedRange = $startDate->format('Y-m-d') . ' to ' . $endDate->format('Y-m-d');
        $diffInDays = $startDate->diffInDays($endDate);

        // 2. DATA REVENUE (DYNAMIC GROUPING)
        // Ambil data mentah harian dulu
        $rawRevenue = Invoice::select(
            DB::raw("DATE(created_at) as date"),
            DB::raw('SUM(amount) as total')
        )
            ->where('status', 'paid')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $chartDates = [];
        $chartValues = [];

        // --- LOGIC GROUPING ---
        if ($diffInDays <= 60) {
            // MODE: DAILY
            $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
            foreach ($period as $date) {
                $d = $date->format('Y-m-d');
                $chartDates[] = $date->format('d M');
                $chartValues[] = $rawRevenue[$d] ?? 0;
            }
        } elseif ($diffInDays > 60 && $diffInDays <= 120) {
            // MODE: PER 3 DAYS
            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $chunkEnd = $currentDate->copy()->addDays(2);
                if ($chunkEnd > $endDate) $chunkEnd = $endDate->copy();

                $sum = 0;
                // Loop internal untuk sum 3 hari
                $tempDate = $currentDate->copy();
                while ($tempDate <= $chunkEnd) {
                    $sum += $rawRevenue[$tempDate->format('Y-m-d')] ?? 0;
                    $tempDate->addDay();
                }

                $chartDates[] = $currentDate->format('d M') . ' - ' . $chunkEnd->format('d M');
                $chartValues[] = $sum;

                $currentDate->addDays(3);
            }
        } else {
            // MODE: WEEKLY (> 120 Hari / 4 Bulan)
            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $chunkEnd = $currentDate->copy()->addDays(6);
                if ($chunkEnd > $endDate) $chunkEnd = $endDate->copy();

                $sum = 0;
                $tempDate = $currentDate->copy();
                while ($tempDate <= $chunkEnd) {
                    $sum += $rawRevenue[$tempDate->format('Y-m-d')] ?? 0;
                    $tempDate->addDay();
                }

                $chartDates[] = $currentDate->format('d M') . ' - ' . $chunkEnd->format('d M');
                $chartValues[] = $sum;

                $currentDate->addDays(7);
            }
        }

        $chartRevenue = [
            'labels' => $chartDates,
            'data' => $chartValues
        ];

        // 3. STATISTIK RINGKAS
        $summary = [
            'total_revenue' => Invoice::where('status', 'paid')->whereBetween('created_at', [$startDate, $endDate])->sum('amount'),
            'total_projects' => Task::whereBetween('created_at', [$startDate, $endDate])->count(),
            'avg_deal' => Invoice::where('status', 'paid')->whereBetween('created_at', [$startDate, $endDate])->avg('amount') ?? 0,
        ];

        // 4. TOP SERVICES
        $topServices = Service::withCount(['tasks' => function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        }])
            ->orderByDesc('tasks_count')
            ->take(5) // Limit 5 untuk tabel
            ->get();

        // 5. PROJECT STATUS DISTRIBUTION
        $statusStats = Task::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $chartStatus = [
            $statusStats['active'] ?? 0,
            $statusStats['queue'] ?? 0,
            $statusStats['completed'] ?? 0,
            $statusStats['revision'] ?? 0
        ];

        // 6. TOP SPENDER CLIENTS
        $topClients = User::where('role', 'client')
            ->with(['wallet'])
            ->get()
            ->map(function ($user) use ($startDate, $endDate) {
                $user->total_spent = Invoice::where('user_id', $user->id)
                    ->where('status', 'paid')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->sum('amount');
                return $user;
            })
            ->sortByDesc('total_spent')
            ->values()
            ->take(10);

        // 7. TOP STAFF PERFORMANCE
        $topStaff = User::where('role', 'staff')
            ->get()
            ->map(function ($user) use ($startDate, $endDate) {
                $user->earned_tokens = Task::where('assignee_id', $user->id)
                    ->where('status', 'completed')
                    ->whereBetween('completed_at', [$startDate, $endDate])
                    ->sum('toratix_locked');

                $user->completed_count = Task::where('assignee_id', $user->id)
                    ->where('status', 'completed')
                    ->whereBetween('completed_at', [$startDate, $endDate])
                    ->count();
                return $user;
            })
            ->sortByDesc('earned_tokens')
            ->values()
            ->take(10);

        return view('admin.analytics.index', compact(
            'chartRevenue',
            'chartStatus',
            'topClients',
            'summary',
            'selectedRange',
            'topStaff',
            'topServices'
        ));
    }

    /**
     * Export PDF Report
     */
    public function exportPdf(Request $request)
    {
        if ($request->has('date_range') && !empty($request->date_range)) {
            $dates = explode(' to ', $request->date_range);
            $startDate = Carbon::parse($dates[0])->startOfDay();
            $endDate = isset($dates[1]) ? Carbon::parse($dates[1])->endOfDay() : $startDate->endOfDay();
        } else {
            $startDate = now()->subMonths(6)->startOfDay();
            $endDate = now()->endOfDay();
        }

        // --- RE-USE LOGIC (Simplified for PDF) ---
        $summary = [
            'total_revenue' => Invoice::where('status', 'paid')->whereBetween('created_at', [$startDate, $endDate])->sum('amount'),
            'total_projects' => Task::whereBetween('created_at', [$startDate, $endDate])->count(),
            'avg_deal' => Invoice::where('status', 'paid')->whereBetween('created_at', [$startDate, $endDate])->avg('amount') ?? 0,
        ];

        // Top Services Table
        $topServices = Service::withCount(['tasks' => function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        }])->orderByDesc('tasks_count')->take(10)->get();

        // Top Staff
        $topStaff = User::where('role', 'staff')->get()->map(function ($user) use ($startDate, $endDate) {
            $user->earned_tokens = Task::where('assignee_id', $user->id)
                ->where('status', 'completed')->whereBetween('completed_at', [$startDate, $endDate])->sum('toratix_locked');
            $user->completed_count = Task::where('assignee_id', $user->id)
                ->where('status', 'completed')->whereBetween('completed_at', [$startDate, $endDate])->count();
            return $user;
        })->sortByDesc('earned_tokens')->take(10);

        // Top Clients
        $topClients = User::where('role', 'client')->get()->map(function ($user) use ($startDate, $endDate) {
            $user->total_spent = Invoice::where('user_id', $user->id)
                ->where('status', 'paid')->whereBetween('created_at', [$startDate, $endDate])->sum('amount');
            return $user;
        })->sortByDesc('total_spent')->take(10);

        $pdf = Pdf::loadView('admin.analytics.pdf', compact('summary', 'topStaff', 'topClients', 'topServices', 'startDate', 'endDate'));
        return $pdf->download('Vektora-Report-' . now()->format('Ymd') . '.pdf');
    }
}
