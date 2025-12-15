<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil List Unique Actions untuk Filter Dropdown
        $actions = AuditLog::select('action')->distinct()->pluck('action');

        // 2. Query Utama
        $query = AuditLog::with('user')->orderBy('created_at', 'desc');

        // Filter: Search (User / Description / IP)
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'ilike', "%{$search}%")
                    ->orWhere('ip_address', 'ilike', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('full_name', 'ilike', "%{$search}%");
                    });
            });
        }

        // Filter: Action Type
        if ($request->action && $request->action !== 'all') {
            $query->where('action', $request->action);
        }

        // Filter: Date Range (Optional, contoh sederhana hari ini)
        // if ($request->date) ...

        $logs = $query->paginate(20)->withQueryString();

        // 3. Statistik Sederhana (Bento Grid)
        $stats = [
            'today_count' => AuditLog::whereDate('created_at', today())->count(),
            'unique_users' => AuditLog::whereDate('created_at', today())->distinct('user_id')->count('user_id'),
        ];

        return view('admin.audit.index', compact('logs', 'actions', 'stats'));
    }
}
