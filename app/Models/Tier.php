<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tier extends Model
{
    protected $fillable = ['name', 'min_toratix', 'max_toratix', 'max_active_tasks', 'benefits', 'max_workspaces'];

    protected $casts = [
        'benefits' => 'array', // Agar JSON otomatis jadi Array PHP
    ];
}
