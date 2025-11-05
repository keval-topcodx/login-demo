<?php

namespace App\Services;

use App\Models\Giftcard;
use App\Models\Order;

class RefundService
{
    public function refundAmount(Order $order, float $amount_to_collect, float &$discount)
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
        if($refundAmount > 0) {
            $used_credit = $order->discounts()->where('name', '=', 'User Credits')->first();
            if($used_credit) {
                $credit_to_refund = (float) min(abs($used_credit->amount), $refundAmount);

                $previousBalance = $user->credits;
                $user->increment('credits', $credit_to_refund);

                $discount += $credit_to_refund;

                $user->logs()->create([
                    'credit_amount' => $credit_to_refund,
                    'previous_balance' => $previousBalance,
                    'new_balance' => $previousBalance + $credit_to_refund,
                    'description' => 'Refund for order:' . $order->id,
                ]);

                if (abs($used_credit->amount) === $credit_to_refund) {
                    $used_credit->delete();
                } else {
                    $used_credit->update([
                        'amount' => $used_credit->amount + $credit_to_refund
                    ]);
                }
                $refundAmount -= $credit_to_refund;
            }
        }

        if($refundAmount > 0) {
            $giftcard = $order->discounts()->where('name', 'like', 'giftcard%')->first();

            if ($giftcard) {
                $giftcard_to_return = (float)  min(abs($giftcard->amount), $refundAmount);
                Giftcard::where('code', $giftcard->code)->increment('balance', $giftcard_to_return);

                $discount += $giftcard_to_return;

                if (abs($giftcard->amount) === $giftcard) {
                    $giftcard->delete();
                } else {
                    $giftcard->update([
                       'amount' =>  $giftcard->amount + $giftcard_to_return
                    ]);
                }
            }
        }
    }
}
