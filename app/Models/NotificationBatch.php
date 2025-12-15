<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationBatch extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false; // Karena kita handle created_at manual/default DB

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'batch_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
