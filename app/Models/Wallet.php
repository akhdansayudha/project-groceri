<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Wallet extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['user_id', 'balance', 'total_purchased', 'current_tier_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tier()
    {
        return $this->belongsTo(Tier::class, 'current_tier_id');
    }

    // TAMBAHKAN INI
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
