<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
use App\Models\Task;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

Broadcast::channel('chat.{taskId}', function ($user, $taskId) {
    // 1. Debugging Info (Cek di storage/logs/laravel.log)
    Log::info("--- BROADCAST AUTH CHECK ---");
    Log::info("User Login: " . $user->id . " (" . $user->role . ")");
    Log::info("Target Task: " . $taskId);

    // 2. Gunakan 'where' manual untuk memastikan UUID terbaca sebagai string
    $task = Task::where('id', $taskId)->first();

    if (!$task) {
        Log::error("GAGAL: Task tidak ditemukan di Database.");
        return false;
    }

    // 3. Normalisasi ID ke String (PENTING untuk UUID!)
    // Kita memaksa semua ID menjadi string agar operator '===' bekerja valid
    $userId = (string) $user->id;
    $ownerId = (string) $task->user_id;
    $assigneeId = (string) $task->assignee_id;

    // 4. Logika Izin
    $isOwner = $userId === $ownerId;
    $isAssignee = $userId === $assigneeId;
    $isAdmin = $user->role === 'admin';

    Log::info("Check: Owner? " . ($isOwner ? 'Y' : 'N') .
        " | Assignee? " . ($isAssignee ? 'Y' : 'N') .
        " | Admin? " . ($isAdmin ? 'Y' : 'N'));

    if ($isOwner || $isAssignee || $isAdmin) {
        return true;
    }

    Log::error("GAGAL: Akses Ditolak.");
    return false;
});
