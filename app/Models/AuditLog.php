<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';
    protected $guarded = ['id'];
    public $timestamps = false; // Kita handle created_at manual saat insert

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
