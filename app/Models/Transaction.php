<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // PENTING: UUID

class Transaction extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'wallet_id',
        'type',        // 'credit' (Topup) atau 'debit' (Usage)
        'amount',      // Jumlah token
        'description', // Keterangan transaksi
        'reference_id' // ID Task atau Invoice terkait
    ];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
