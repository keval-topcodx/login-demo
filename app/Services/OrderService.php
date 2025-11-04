<?php

namespace App\Services;

use App\Models\Order;

class OrderService
{
    public function updateOrderAndItems(Order $order, array $orderData, float $discount, $paymentId, $amount_to_collect)
    {
        $order->items()->delete();
        $subtotal = 0;

        foreach ($orderData as $item) {
            $subtotal += $item['quantity'] * $item['price'];
            $order->items()->create([
            'variant_id' => $item['variant'],
            'price'      => $item['price'],
            'quantity'   => $item['quantity']
            ]);
        }

        $total = $subtotal + $discount;
        $order->update([
        'total'    => $total,
        'amount_paid' => $total,
        ]);
        if($amount_to_collect > 0) {
            $order->payments()->create([
                'payment_id' => $paymentId,
                'amount' => $amount_to_collect,
                'refunded_amount' => 0,
            ]);
        }

    }
}
