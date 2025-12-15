<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Workspace;
use App\Models\Task;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class WorkspaceController extends Controller
{
    /**
     * Helper Private: Hitung estimasi size berdasarkan jumlah attachment
     * Asumsi: 1 File Rata-rata = 5 MB
     */
    private function calculateEstimatedStorage($tasks)
    {
        $totalFiles = 0;
        foreach ($tasks as $task) {
            // Decode JSON attachments
            $files = is_string($task->attachments) ? json_decode($task->attachments, true) : $task->attachments;
            if (is_array($files)) {
                $totalFiles += count($files);
            }
        }

        // Kalkulasi: Total File * 5 MB
        $totalSizeMB = $totalFiles * 2;

        // Format output (GB jika > 1000MB, else MB)
        if ($totalSizeMB >= 1000) {
            return number_format($totalSizeMB / 1024, 2) . ' GB';
        }

        return $totalSizeMB . ' MB';
    }

    public function index(Request $request)
    {
        $search = $request->query('search');

        // Query Utama
        // Kita perlu 'with(tasks)' untuk mengambil data attachments guna perhitungan size di card
        $query = Workspace::with(['user', 'tasks' => function ($q) {
            $q->select('id', 'workspace_id', 'attachments', 'status');
        }])
            ->withCount(['tasks as total_projects'])
            ->withCount(['tasks as active_projects' => function ($query) {
                $query->whereIn('status', ['active', 'in_progress', 'review', 'revision']);
            }]);

        // Logic Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                    ->orWhereHas('user', function ($subQ) use ($search) {
                        $subQ->where('full_name', 'ilike', "%{$search}%");
                    });
            });
        }

        // Ambil Data Workspace (Paginate)
        $workspaces = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        // --- STATISTIK GLOBAL (HEADER) ---
        $totalWorkspaces = Workspace::count();
        $totalProjectsGlobal = Task::count();

        // Hitung Global Storage (Ambil semua task yang punya attachment)
        // Menggunakan chunk untuk hemat memory jika data ribuan
        $globalFilesCount = 0;
        Task::select('attachments')->whereNotNull('attachments')->chunk(200, function ($tasks) use (&$globalFilesCount) {
            foreach ($tasks as $task) {
                $files = is_string($task->attachments) ? json_decode($task->attachments, true) : $task->attachments;
                if (is_array($files)) {
                    $globalFilesCount += count($files);
                }
            }
        });

        // Hitung Global Size (Count * 5MB)
        $globalSizeMB = $globalFilesCount * 2;
        $totalStorage = ($globalSizeMB >= 1000)
            ? number_format($globalSizeMB / 1024, 2) . ' GB'
            : $globalSizeMB . ' MB';

        // Inject calculated storage ke setiap object workspace untuk view
        foreach ($workspaces as $ws) {
            $ws->storage_est = $this->calculateEstimatedStorage($ws->tasks);
        }

        return view('admin.workspaces.index', compact('workspaces', 'totalWorkspaces', 'totalProjectsGlobal', 'totalStorage'));
    }

    // Opsional: Jika ingin melihat detail isi workspace (bisa diarahkan ke filter project)
    public function show($id)
    {
        $workspace = Workspace::with(['user', 'tasks.service'])->findOrFail($id);

        // Hitung Storage Lokal Workspace ini
        $storageEst = $this->calculateEstimatedStorage($workspace->tasks);

        // Statistik Lokal
        $stats = [
            'total' => $workspace->tasks()->count(),
            'queue' => $workspace->tasks()->where('status', 'queue')->count(),
            'active' => $workspace->tasks()->whereIn('status', ['active', 'in_progress', 'review', 'revision'])->count(),
            'completed' => $workspace->tasks()->where('status', 'completed')->count(),
            'storage' => $storageEst
        ];

        // List Project di dalamnya
        $tasks = $workspace->tasks()
            ->with(['user', 'service', 'assignee']) // Eager Load relasi
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.workspaces.show', compact('workspace', 'stats', 'tasks'));
    }
}
