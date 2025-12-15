<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Workspace;
use App\Models\User; // Import Model User

class WorkspaceController extends Controller
{
    /**
     * Menampilkan daftar workspace (List Folder)
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Ambil workspace user beserta hitungan jumlah project di dalamnya
        $workspaces = $user->workspaces()->withCount('tasks')->orderBy('created_at', 'desc')->get();

        // Safety check untuk limit tier
        $maxWorkspaces = $user->wallet->tier->max_workspaces ?? 1;

        return view('client.workspaces.index', compact('workspaces', 'maxWorkspaces'));
    }

    /**
     * Menampilkan detail satu workspace (Dashboard Mini)
     */
    public function show(Workspace $workspace)
    {
        // 1. Security Check: Pastikan workspace milik user yang login
        if ($workspace->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this workspace.');
        }

        // 2. Hitung Statistik Task
        $stats = [
            'total' => $workspace->tasks()->count(),
            'queue' => $workspace->tasks()->where('status', 'queue')->count(),
            'active' => $workspace->tasks()->whereIn('status', ['active', 'in_progress', 'review', 'revision'])->count(),
            'completed' => $workspace->tasks()->where('status', 'completed')->count(),
        ];

        // 3. Ambil List Project di Workspace ini
        $tasks = $workspace->tasks()->with('service')->orderBy('updated_at', 'desc')->get();

        return view('client.workspaces.show', compact('workspace', 'stats', 'tasks'));
    }

    /**
     * Membuat Workspace Baru
     */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:50']);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Cek Limit Tier
        $limit = $user->wallet->tier->max_workspaces ?? 1;
        if ($user->workspaces()->count() >= $limit) {
            return back()->with('error', "Limit tercapai! Tier Anda hanya bisa membuat $limit workspace.");
        }

        Workspace::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'description' => $request->description ?? null
        ]);

        return back()->with('success', 'Workspace berhasil dibuat!');
    }

    /**
     * Menghapus Workspace
     */
    public function destroy(Workspace $workspace)
    {
        // 1. Security Check: Pastikan pemiliknya benar
        if ($workspace->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // 2. Hapus Workspace
        // Catatan: Pastikan di database Foreign Key tasks -> workspace_id di set ON DELETE SET NULL atau CASCADE
        // Jika CASCADE, semua task di dalamnya akan hilang. 
        // Jika SET NULL, task akan menjadi tanpa workspace.
        $workspace->delete();

        return redirect()->route('client.workspaces.index')
            ->with('success', 'Workspace berhasil dihapus.');
    }
}
