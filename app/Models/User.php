<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // Penting untuk UUID

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUuids;

    // Karena di database tipe ID adalah UUID, bukan Auto Increment Integer
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',      // Mapping ke 'full_name' perlu diperhatikan
        'full_name', // Sesuaikan dengan nama kolom di DB Anda
        'email',
        'password',
        'avatar_url',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Accessor untuk mengubah path avatar menjadi Full URL secara otomatis
     */
    public function getAvatarUrlAttribute($value)
    {
        // 1. Jika kosong, kembalikan null
        if (!$value) {
            return null;
        }

        // 2. Jika value sudah berupa link http/https, kembalikan as is
        if (str_starts_with($value, 'http')) {
            return $value;
        }

        // 3. (REVISI) Generate URL manual dari config untuk menghindari error editor
        $baseUrl = config('filesystems.disks.supabase.url');
        
        // Gabungkan Base URL + Path File (misal: https://.../profiles/foto.jpg)
        return rtrim($baseUrl, '/') . '/' . ltrim($value, '/');
    }

    // Relasi yang sudah ada
    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
    public function workspaces()
    {
        return $this->hasMany(Workspace::class);
    }
    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assignee_id');
    }
}
