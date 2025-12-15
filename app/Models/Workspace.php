<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // <--- 1. Import Ini

class Workspace extends Model
{
    use HasFactory, HasUuids; // <--- 2. Pasang Trait Ini

    protected $fillable = ['user_id', 'name', 'description'];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Task
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
