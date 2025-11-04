<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
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
        if (\Cart::isEmpty()) {
            return redirect()->back()->with('message', 'Your cart is empty!');
        }
        $cartCollection = \Cart::getContent();
        $cartSubTotal = \Cart::getSubTotal();
//        $cartTotal = \Cart::getTotal();
        $cartTotal = number_format(\Cart::getTotal(), 2, '.', '');
        $cartConditions = \Cart::getConditions();
        $lastOrder = auth()->user()->orders()->latest()->first();
        $shippingInfo = json_decode($lastOrder->shipping_address, true);


        return view('order.display', ['cartItems' => $cartCollection, 'subtotal' => $cartSubTotal, 'total' => $cartTotal, 'cartConditions' => $cartConditions, 'shippingInfo' => $shippingInfo,
        ]);
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
            return redirect()->route('menu.index')->with('success', 'Your Cart is empty.');
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
        $discount = isset($order->discount->amount) ? $order->discount->amount : 0;
        $amount_paid = (int) $order->amount_paid;

        $user = auth()->user();
        $paymentMethod = $user->paymentMethods()->first();

        if($action == "chargeAmount") {
            DB::beginTransaction();

            try {
                $stripeCharge = $request->user()->charge(
                    $amount_to_collect * 100,
                    $paymentMethod->id,
                    [
                        'return_url' => route('menu.index'),
                    ]
                );
                $paymentId = $stripeCharge->id;

                $orderService->updateOrderAndItems($order, $orderData, $discount, $paymentId, $amount_to_collect);
                DB::commit();
                return response()->json([
                    'status' => 200,
                    'message' => 'Payment successful. Order updated.'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 400,
                    'error' => $e->getMessage()
                ]);
            }
        } elseif ($action == "refundAmount") {
            $refundObject = new refundService;
            $refundObject->refundAmount($order, $amount_to_collect);
            $orderService->updateOrderAndItems($order, $orderData, $discount, 0, 0);

            return response()->json([
                'status' => 200,
                'message' => 'Refund successful. Order updated.'
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
        $remove = \Cart::clearCartConditions();
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
