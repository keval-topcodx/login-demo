<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Darryldecode\Cart\CartCondition;

class CreditService
{
    public function applyCredits()
    {
        \Cart::removeConditionsByType('credits');
        $user = Auth::user();
        $userCredits = $user->credits ?? 0;
        $cartTotal = \Cart::getTotal();

        if ($userCredits <= 0 || $cartTotal <= 0) {
            \Cart::removeConditionsByType('credits');
            return ['valid' => false];
        }

        $usableCredits = min($cartTotal, $userCredits);

        $creditCondition = new CartCondition([
            'name' => 'User credits',
            'type' => 'credits',
            'target' => 'total',
            'value' => -$usableCredits,
        ]);

        \Cart::condition($creditCondition);

        return [
            'valid' => true,
            'credits_used' => $usableCredits,
            'remaining_credits' => $userCredits - $usableCredits,
        ];
    }

}
