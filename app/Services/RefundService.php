<?php

namespace App\Services;

use App\Models\Order;

class RefundService
{
    public function refundAmount(Order $order, float $amount_to_collect)
    {
        $user = auth()->user();
        $refundAmount = abs($amount_to_collect);
        $payments = $order->payments()
            ->orderByDesc('created_at')
            ->get();

        foreach ($payments as $payment) {
            if ($refundAmount <= 0) {
                break;
            }

            $remaining = $payment->amount - $payment->refunded_amount;

            if($remaining <= 0) {
                continue;
            }
            $amount_to_refund = min($refundAmount, $remaining);
            $user->refund($payment->payment_id, [
                'amount' => (int) ($amount_to_refund * 100),
            ]);

            $refundAmount -= $amount_to_refund;
            $payment->refunded_amount += $amount_to_refund;
            $payment->save();
        }
    }
}
