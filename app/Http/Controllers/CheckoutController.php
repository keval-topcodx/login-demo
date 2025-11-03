<?php

namespace App\Http\Controllers;

use App\Models\Giftcard;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class CheckoutController extends Controller
{
    public function index()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $amount = \Cart::getTotal();

        $paymentIntent = PaymentIntent::create([
            'amount' => ($amount * 100), // Amount in cents
            'currency' => 'usd',
            'automatic_payment_methods' => ['enabled' => true],
        ]);

        return view('checkout.index', ['clientSecret' => $paymentIntent->client_secret]);
    }

    public function createOrder(Request $request)
    {
        $user = Auth::user();
        $userId = $user['id'];
        $data = $request->input();
        $paymentId = $data['paymentId'];
        $cartCollection = \Cart::getContent();
        $cartSubTotal = \Cart::getSubTotal();
        $cartTotal = \Cart::getTotal();
        $firstItem = \Cart::getContent()->first();
        $condition = \Cart::getConditionsByType('giftcard')->first();
        $shipping_address = $firstItem->attributes->shipping_address;
//        $shipping_address = $cartCollection["attributes"]["shipping_address"];



        $order = Order::create([
            'user_id' => $userId,
            'shipping_address' => $shipping_address,
            'subtotal' => $cartSubTotal,
        ]);

        foreach ($cartCollection as $item) {
            $variantId = $item['id'];
            $price = $item['price'];
            $quantity = $item['quantity'];

            $order->items()->create([
                'variant_id' => $variantId,
                'price' => $price,
                'quantity' => $quantity,
            ]);
        }

        if ($condition) {
            $order->discount()->create([
                'name'   => $condition->getType() . ' - ' . $condition->getName(),
                'code'   => $condition->getName(),
                'amount' => $condition->getValue(),
            ]);

            $giftcard = Giftcard::where('code', $condition->getName())->first();

            if ($giftcard) {
                $usedAmount = abs($condition->getValue());
                $newBalance = max(0, $giftcard->balance - $usedAmount);

                $giftcard->update(['balance' => $newBalance]);
            }
        }

        $order->payment()->create([
            'payment_id' => $paymentId,
            'amount' => $cartTotal,
            'refunded_amount' => 0,
        ]);
        \Cart::clearCartConditions();
        \Cart::clear();
        return response()->json([
            'status' => 200,
            'message' => 'Payment Successful. Order has been created.'
        ]);
    }
}
