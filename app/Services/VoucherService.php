<?php

namespace App\Services;

use App\Models\Giftcard;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class VoucherService
{
    protected $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function applyVoucher()
    {
        $userId = Auth::user()->id;

        $giftcard = Giftcard::where('code', $this->code)->first();

        if (! $giftcard) {
            return ['valid' => false, 'message' => 'Gift card not found.'];
        }

        if ($giftcard->status == 0) {
            return ['valid' => false, 'message' => 'Gift card status is disabled.'];
        }

        if ($giftcard->user_id !== "anyone" && $giftcard->user_id !== $userId) {
            return ['valid' => false, 'message' => 'Gift card is not available for this user.'];
        }

        if($giftcard->balance <= 0) {
            return ['valid' => false, 'message' => 'Gift card has no balance left.'];
        }

        if($giftcard->expiry_date < today()) {
            return ['valid' => false, 'message' => 'Gift card has expired.'];
        }

        $cartSubTotal = \Cart::getSubTotal();
        $giftCardValue = min($giftcard->balance, $cartSubTotal);

        $giftCardConditions = \Cart::getConditionsByType('giftcard');

        if ($giftCardConditions->isEmpty()) {
            $condition = new \Darryldecode\Cart\CartCondition([
                'name' => $giftcard->code,
                'type' => 'giftcard',
                'target' => 'total',
                'value' => -$giftCardValue,
            ]);
            \Cart::condition($condition);

            return ['valid' => true, 'giftcard' => $giftcard];
        } else {
            $condition = \Cart::getConditionsByType('giftcard')->first();
            $name = $condition->getName();
            $appliedGiftCard = Giftcard::where('code', $name)->first();
            return ['valid' => true, 'giftcard' => $appliedGiftCard];
        }
    }

}
