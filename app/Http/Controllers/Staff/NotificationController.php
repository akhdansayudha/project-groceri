<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Menandai satu notifikasi sebagai sudah dibaca (AJAX)
     */
    public function markAsRead($id)
    {
        // Pastikan notifikasi milik staff yang sedang login
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);

        $notification->update(['is_read' => true]);

        return response()->json(['success' => true, 'message' => 'Notification marked as read']);
    }

    /**
     * Menandai semua notifikasi sebagai sudah dibaca (AJAX)
     */
    public function markAllRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true, 'message' => 'All notifications marked as read']);
    }
}
