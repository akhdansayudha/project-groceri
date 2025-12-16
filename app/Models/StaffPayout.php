<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffPayout extends Model
{
    use HasFactory;

    // Nama tabel di database (sesuai SQL yang Anda jalankan sebelumnya)
    protected $table = 'staff_payouts';

    // Kolom yang boleh diisi (Mass Assignment)
    protected $fillable = [
        'user_id',
        'amount_token',
        'amount_currency',
        'status',
        'proof_url', // <--- PASTIKAN INI ADA
        'admin_note',
        'bank_name',
        'bank_account',
        'bank_holder'
    ];

    // Casting tipe data (Opsional, agar format data konsisten)
    protected $casts = [
        'amount_currency' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke User (Staff)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
