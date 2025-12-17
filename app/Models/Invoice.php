<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Invoice extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'invoice_number',
        'amount',
        'status',
        'description',
        'payment_method',
        'paid_at',
        'due_date',
        'payment_link',
        'snap_token',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'due_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
