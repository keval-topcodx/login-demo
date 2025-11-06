<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\DB;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'title',
        'price',
        'sku',
        'created_at',
        'updated_at',
    ];

    protected function price(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
            $user = auth()->user();

            if ($user && $user->hasRole('wholesaler')) {
                $variant = $user->productVariants()->find($this->id);

                if ($variant) {
                    return $variant->pivot->price;
                }
            }
            return $value;
        }
        );
    }


    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
