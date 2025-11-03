<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Order extends Model
{
    protected $fillable = [
        'user_id',
        'shipping_address',
        'subtotal',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class)->chaperone();
    }

    public function payment(): HasOne
    {
        return $this->hasOne(OrderPayments::class);
    }

    public function discount(): HasOne
    {
        return $this->hasOne(OrderDiscount::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
