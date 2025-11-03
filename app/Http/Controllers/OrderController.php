<?php

namespace App\Http\Controllers;

use App\Models\ProductVariant;
use Illuminate\Http\Request;
use App\Http\Requests\CreateOrderRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use App\Services\VoucherService;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cartCollection = \Cart::getContent();
        $cartSubTotal = \Cart::getSubTotal();
//        $cartTotal = \Cart::getTotal();
        $cartTotal = number_format(\Cart::getTotal(), 2, '.', '');
        $cartConditions = \Cart::getConditions();

        return view('order.display', ['cartItems' => $cartCollection, 'subtotal' => $cartSubTotal, 'total' => $cartTotal, 'cartConditions' => $cartConditions]);
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
//        \Cart::clearCartConditions();
//        \Cart::clear();
        $orderItems = $order->items;
//        foreach ($orderItems as $orderItem) {
//            \Cart::add([
//                'id'       => $orderItem->variant_id,
//                'name'     => $orderItem->variant->product->title,
//                'price'    => $orderItem->price,
//                'quantity' => $orderItem->quantity,
//                'attributes' => array(
//                    'size' => $orderItem->variant->title,
//                    'image' => $orderItem->variant->product->image_urls,
//                    'shipping_address' => '',
//                )
//            ]);
//        }
        return view('order.edit', ['order' => $order, 'orderItems' => $orderItems]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update( Request $request, Order $order)
    {
        //
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


//        foreach ($variants as $variant) {
//            $existingItem = \Cart::get($variant->id);
//
//            if ($existingItem) {
//                \Cart::update($variant->id, [
//                    'quantity' => 1
//                ]);
//            } else {
//                \Cart::add([
//                    'id'       => $variant->id,
//                    'name'     => $variant->product->title,
//                    'price'    => $variant->price ?? 0,
//                    'quantity' => 1,
//                    'attributes' => [
//                        'size' => $variant->title,
//                        'image' => $variant->product->image_urls[0] ?? null,
//                        'shipping_address' => '',
//                    ]
//                ]);
//            }
//        }
////        dd(\Cart::getContent());
        return response()->json([
           'success' => true,
            'variants' => $variants,
        ]);
    }
}
