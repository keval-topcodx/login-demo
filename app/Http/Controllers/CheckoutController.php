<?php

namespace App\Http\Controllers;

use App\Models\Giftcard;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Customer;


class CheckoutController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $amount = \Cart::getTotal();

        if (empty($user->stripe_id)){
            $user->createAsStripeCustomer();
        }

        $paymentIntent = PaymentIntent::create([
            'amount' => ($amount * 100),
            'currency' => 'usd',
            'automatic_payment_methods' => ['enabled' => true],
//            'automatic_payment_methods' => [
//                'enabled' => true,
//                'allow_redirects' => 'never'
//            ],
            'customer' => $user->stripe_id,
            'setup_future_usage' => 'off_session',
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
        $creditCondition = \Cart::getConditionsByType('credits')->first();
        $shipping_address = $firstItem->attributes->shipping_address;


        $order = Order::create([
            'user_id' => $userId,
            'shipping_address' => $shipping_address,
            'total' => $cartTotal,
            'amount_paid' => $cartTotal,
        ]);
        $user->update([
            'shipping_address' => json_decode($shipping_address),
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
            $order->discounts()->create([
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

        if($creditCondition) {
            $order->discounts()->create([
               'name' => 'User Credits',
               'code' => 'CREDITS - ' . $userId,
               'amount' => $creditCondition->getValue(),
            ]);
            $previous_credits = $user->credits;
            $credit_amount = $creditCondition->getValue();
            $new_credits = $previous_credits + $credit_amount;

            $user->increment('credits', $credit_amount);

            $user->logs()->create([
                'credit_amount' => $credit_amount,
                'previous_balance' => $previous_credits,
                'new_balance' => $new_credits,
                'description' => "$" . abs($credit_amount) . " used in order:" . $order->id,
            ]);
        }

        $order->payments()->create([
            'payment_id' => $paymentId,
            'amount' => $cartTotal,
            'refunded_amount' => 0,
        ]);
        \Cart::clearCartConditions();
        \Cart::clear();
        return response()->json([
            'status' => 200,
            'message' => 'Payment Successful. Order has been created.',
            'redirect_url' => route('menu.index'),
        ]);

    }

    public function updateOrderCheckout(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $amount = $request->input("amount");

        $paymentIntent = PaymentIntent::create([
            'amount' => ($amount * 100), // Amount in cents
            'currency' => 'usd',
            'automatic_payment_methods' => ['enabled' => true],
        ]);

        return view('checkout.index', ['clientSecret' => $paymentIntent->client_secret]);
    }
}
