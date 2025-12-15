<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\NotificationBatch;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        // Ambil list Batch beserta hitungan statistik (Total Terkirim & Total Dibaca)
        $batches = NotificationBatch::with('sender')
            ->withCount(['notifications as total_sent'])
            ->withCount(['notifications as total_read' => function ($query) {
                $query->where('is_read', true);
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.notifications.index', compact('batches'));
    }

    public function create()
    {
        return view('admin.notifications.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,success,warning,error,promo',
            'target' => 'required|in:all_users,client,staff'
        ]);

        DB::transaction(function () use ($request) {
            // 1. Simpan Parent Batch (Menyimpan Template Asli)
            $batch = NotificationBatch::create([
                'title' => $request->title,
                'message' => $request->message, // Simpan template mentah: "Halo {nama}"
                'type' => $request->type,
                'target_audience' => $request->target,
                'sender_id' => Auth::id(),
                'created_at' => now(),
            ]);

            // 2. Ambil Target User (Select nama & email juga untuk replace)
            $query = User::query();
            if ($request->target == 'client') $query->where('role', 'client');
            if ($request->target == 'staff') $query->where('role', 'staff');

            // Kita butuh data full_name dan email untuk proses replace
            $users = $query->select('id', 'full_name', 'email')->get();

            // 3. Batch Insert Notifications (Child)
            $notificationsData = [];
            $now = now();

            foreach ($users as $user) {
                // LOGIC PERSONALISASI
                // Ganti {nama} dengan nama user, {email} dengan email user
                $personalMessage = str_replace(
                    ['{nama}', '{email}'],
                    [$user->full_name, $user->email],
                    $request->message
                );

                $notificationsData[] = [
                    'user_id' => $user->id,
                    'batch_id' => $batch->id,
                    'title' => $request->title,
                    'message' => $personalMessage, // Simpan pesan yang SUDAH dipersonalisasi
                    'type' => $request->type,
                    'is_read' => false,
                    'created_at' => $now,
                ];
            }

            // Insert per 500 baris agar memori aman
            foreach (array_chunk($notificationsData, 500) as $chunk) {
                Notification::insert($chunk);
            }
        });

        return redirect()->route('admin.notifications.index')->with('success', 'Broadcast berhasil dijadwalkan & dikirim.');
    }

    public function show($id)
    {
        // Detail Batch
        $batch = NotificationBatch::withCount(['notifications as total_sent'])
            ->withCount(['notifications as total_read' => function ($query) {
                $query->where('is_read', true);
            }])
            ->findOrFail($id);

        // List Penerima di Batch ini
        $recipients = Notification::with('user')
            ->where('batch_id', $id)
            ->orderBy('is_read', 'desc') // Yang sudah baca tampil duluan (opsional)
            ->paginate(15);

        return view('admin.notifications.show', compact('batch', 'recipients'));
    }

    public function destroy($id)
    {
        $batch = NotificationBatch::findOrFail($id);
        $batch->delete(); // Karena 'ON DELETE CASCADE' di DB, anak-anaknya otomatis terhapus
        return back()->with('success', 'Batch notifikasi berhasil dihapus.');
    }
}
