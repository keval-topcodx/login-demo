<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Casts\Attribute;



class Order extends Model
{
    protected $fillable = [
        'user_id',
        'shipping_address',
        'total',
        'amount_paid',
    ];

    protected function subtotal(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->items->sum(fn ($item) => $item->quantity * $item->price)
        );
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class)->chaperone();
    }

    public function payments(): HasMany
    {
        return $this->hasMany(OrderPayments::class)->chaperone();
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(OrderDiscount::class)->chaperone();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
