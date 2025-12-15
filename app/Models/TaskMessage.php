<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskMessage extends Model
{
    use HasFactory;

    protected $table = 'task_messages';

    // Matikan updated_at karena di tabel Anda kolom updated_at tidak ada (opsional, jika error column not found)
    const UPDATED_AT = null;

    protected $fillable = [
        'task_id',
        'sender_id',    // Di DB namanya sender_id (bukan user_id)
        'content',      // Di DB namanya content (bukan message)
        'attachment_url', // Di DB namanya attachment_url (bukan attachments)
        'is_read',
        'created_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    // Relasi ke User (Pengirim)
    public function user()
    {
        // Parameter ke-2 harus 'sender_id' karena nama kolom di DB bukan user_id default
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // Accessor untuk mendapatkan Full URL otomatis
    public function getAttachmentUrlAttribute($value)
    {
        if (!$value) return null;

        // Jika value sudah full URL (https://...), kembalikan as is
        if (str_starts_with($value, 'http')) return $value;

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = \Illuminate\Support\Facades\Storage::disk('supabase');

        return $disk->url($value);
    }
}
