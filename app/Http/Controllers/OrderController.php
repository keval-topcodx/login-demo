<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use App\Services\CreditService;
use App\Services\RefundService;
use Illuminate\Http\Request;
use App\Http\Requests\CreateOrderRequest;
use App\Models\Order;
use App\Services\VoucherService;
use Illuminate\Support\Facades\DB;
use App\Services\OrderService;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        app(CreditService::class)->applyCredits();
        if (\Cart::isEmpty()) {
            return redirect()->back()->with('danger', 'Your cart is empty!');
        }
        $cartCollection = \Cart::getContent();
        $cartSubTotal = \Cart::getSubTotal();
        $cartTotal = number_format(\Cart::getTotal(), 2, '.', '');
        $giftcardConditions = \Cart::getConditionsByType('giftcard');
        $creditConditions = \Cart::getConditionsByType('credits');
        $lastOrder = auth()->user()->orders()->latest()->first();
        $shippingInfo = json_decode($lastOrder->shipping_address, true);

        return view('order.display',
            ['cartItems' => $cartCollection, 'subtotal' => $cartSubTotal, 'total' => $cartTotal, 'giftCardConditions' => $giftcardConditions, 'shippingInfo' => $shippingInfo, 'creditConditions' => $creditConditions]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateOrderRequest $request)
    {
        if(\Cart::isEmpty()) {
            return redirect()->route('menu.index')->with('danger', 'Your Cart is empty.');
        }
        if(\Cart::getTotal() <= 0.50) {
            return redirect()->route('order.index')->with('danger', 'Order Total must be more than 0.50 usd.');
        }
        $input = $request->validated();
        $shipping_address = json_encode($input);

        $cartCollection = \Cart::getContent();
        $firstItem = \Cart::getContent()->first();
        $cartConditions = \Cart::getConditions();
        $cartCondition = \Cart::getConditionsByType('giftcard')->first();
        $id = $firstItem->id;

        foreach ($cartCollection as $cartItem) {
            $attributes = $cartItem->attributes->toArray();
            $attributes['shipping_address'] = $shipping_address;
            $cartItemId = $cartItem->id;
            \Cart::update($cartItemId, [
                'attributes' => $attributes,
            ]);
        }

        return redirect()->route('checkout');
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        $orderItems = $order->items;

        return view('order.edit', ['order' => $order, 'orderItems' => $orderItems]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update( Request $request, Order $order, OrderService $orderService)
    {
        $amount_to_collect = (float) $request->input("amount");
        $action = $request->input("action");
        $orderData = $request->input("order");
        $discount = $order->discounts->sum('amount');
        $amount_paid = (float) $order->amount_paid;

        $user = auth()->user();
        $paymentMethod = $user->paymentMethods()->first();

        if($action == "chargeAmount") {
            try {
                $user_credits = isset($user->credits) ? $user->credits : 0;
                if($user_credits > 0) {
                    $usable_credits = min($amount_to_collect, $user_credits);
                    $payment_amount = $amount_to_collect - $usable_credits;

                    $user->decrement('credits', $usable_credits);
                    $discount -= $usable_credits;
                    $user->logs()->create([
                        'credit_amount' => -$usable_credits,
                        'previous_balance' => $user_credits,
                        'new_balance' => $user_credits - $usable_credits,
                        'description' => "$" . abs($usable_credits) . " used in order:" . $order->id,
                    ]);

                    $order->discounts()->where('name', '=', 'User Credits')->decrement('amount', $usable_credits);

                } else {
                    $payment_amount = $amount_to_collect;
                }

                if($payment_amount > 0.50) {
                    $stripeCharge = $request->user()->charge(
                        $payment_amount * 100,
                        $paymentMethod->id,
                        [
                            'return_url' => route('menu.index'),
                        ]
                    );
                    $paymentId = $stripeCharge->id;

                    $orderService->updateOrderAndItems($order, $orderData, $discount, $paymentId, $payment_amount);
                    return response()->json([
                        'status' => 200,
                        'message' => 'Payment successful. Order updated.',
                        'redirect_url' => route('menu.index'),
                    ]);
                } else {
                    $orderService->updateOrderAndItems($order, $orderData, $discount, 0, 0);
                    return redirect()->back()->with('message', 'payment amount of less than 0.50 can not be processed');
                }

            } catch (\Exception $e) {
                return response()->json([
                    'status' => 400,
                    'error' => $e->getMessage()
                ]);
            }
        } elseif ($action == "refundAmount") {
            $refundObject = new refundService;
            $refundObject->refundAmount($order, $amount_to_collect, $discount);
            $orderService->updateOrderAndItems($order, $orderData, $discount, 0, 0);
            return response()->json([
                'status' => 200,
                'message' => 'Refund successful. Order updated.',
                'redirect_url' => route('menu.index'),
            ]);
        }
        return response()->json([
           'error' => 'order not charged',
            'status' => 401,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        dd("Order Delete");
    }

    public function displayUserOrders()
    {
        $orders = Order::with('user')->get();
        return view('user-orders.index', ['orders' => $orders]);
    }

    public function validateCode(Request $request)
    {
        $code = $request->input('code');

        $voucherService = new VoucherService($code);
        $result = $voucherService->applyVoucher();
        $total = \Cart::getTotal();
        if (! $result['valid']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ]);
        }
        return response()->json([
            'success' => true,
            'message' => 'Gift card is valid.',
            'data' => $result['giftcard'],
            'total' => $total,
        ]);
    }

    public function removeGiftCard()
    {
        $removeGiftCard = \Cart::removeConditionsByType('giftcard');
        $cartSubTotal = \Cart::getSubTotal();
        $cartTotal = \Cart::getTotal();
        return response()->json([
            'success' => true,
            'message' => 'Gift card removed.',
            'subtotal' => $cartSubTotal,
            'total' => $cartTotal,
        ]);
    }

    public function addToOrder(Request $request)
    {
        $variantIds = $request->input("variants");
        $variants = ProductVariant::whereIn('id', $variantIds)
            ->with('product')
            ->get();

        return response()->json([
           'success' => true,
            'variants' => $variants,
        ]);
    }

    public function updateOrder(Request $request, Order $order)
    {
        $data = $request->input();
        dd($data);
    }
}
