<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\VoucherService;


class OrderController extends Controller
{
    public function index()
    {
        $cartCollection = \Cart::getContent();
        $cartSubTotal = \Cart::getSubTotal();
//        $cartTotal = \Cart::getTotal();
        $cartTotal = number_format(\Cart::getTotal(), 2, '.', '');
        $cartConditions = \Cart::getConditions();

        return view('order.display', ['cartItems' => $cartCollection, 'subtotal' => $cartSubTotal, 'total' => $cartTotal, 'cartConditions' => $cartConditions]);
    }

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

    public function displayUserOrders()
    {
        $orders = Order::all();
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

}
