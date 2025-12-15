<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Deliverable extends Model
{
    use HasFactory;

    // Karena di SQL tabelnya 'deliverables', Laravel biasanya otomatis mendeteksi.
    // Tapi jika ID-nya UUID, kita perlu setting sedikit.

    protected $fillable = [
        'task_id',
        'staff_id',
        'file_url',
        'file_type',
        'message',
    ];

    // Jika di database (SQL) ID Anda menggunakan UUID:
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    // RELASI BALIK KE TASK
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // RELASI KE STAFF (User)
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
