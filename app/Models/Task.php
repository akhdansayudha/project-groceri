<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\Deliverable;

class Task extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        'user_id',
        'workspace_id',
        'service_id',
        'title',
        'description',
        'brief_data',
        'attachments',
        'status',
        'deadline',
        'toratix_locked',
        'started_at',
        'completed_at',
        'assignee_id',
        'active_at',
        'review_at'
    ];

    protected $casts = [
        'brief_data' => 'array',
        'attachments' => 'array',
        'deadline' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'active_at' => 'datetime',
        'review_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function messages()
    {
        // Relasi ke tabel task_messages, diurutkan dari chat terlama ke terbaru
        return $this->hasMany(TaskMessage::class)->orderBy('created_at', 'asc');
    }

    // Relasi ke Staff yang mengerjakan
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function deliverables()
    {
        return $this->hasMany(Deliverable::class, 'task_id')->orderBy('created_at', 'desc');
    }
}
