<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TokenPrice extends Model
{
    protected $fillable = ['min_qty', 'max_qty', 'price_per_token', 'label'];
}
