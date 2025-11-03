<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Giftcard extends Model
{
    protected $fillable = [
        'code',
        'initial_balance',
        'balance',
        'status',
        'user_id',
        'expiry_date',
    ];
}
