<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'toratix_cost', 'icon_url', 'is_active'];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
