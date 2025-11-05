<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CreditService;
use Illuminate\Http\Request;
use function Carbon\int;

class MenuController extends Controller
{
    public function alreadyInCart(Request $request)
    {
        $data = (int) $request->input('id');
        $cartCollection = \Cart::getContent()->toArray();
        $alreadyInCart = false;
        $quantity = 0;
        foreach ($cartCollection as $item) {
            $id = (int) $item['id'];
            if($data === $id) {
//                dd($item['quantity']);
                $alreadyInCart = true;
                $quantity = $item['quantity'];
                break;
            } else {
                $quantity = 0;
            }
        }
        return response()->json([
            'inCart' => $alreadyInCart,
            'quantity' => $quantity,
        ]);

    }
    public function renderCartSummary()
    {
        $cartCollection = \Cart::getContent();
        $cartTotal = \Cart::getSubTotal();
        $cartTotalQuantity = \Cart::getTotalQuantity();

        return response()->json([
            'cart' => $cartCollection->toArray(),
            'subtotal' => $cartTotal,
            'quantity' => (int) $cartTotalQuantity,
        ]);

    }
    public function index()
    {
        \Cart::removeConditionsByType('credits');
        $products = Product::with(['variants', 'media'])->get();
        $cartCollection = \Cart::getContent();
        $cartSubTotal = \Cart::getSubTotal();
        $cartTotal = number_format(\Cart::getTotal(), 2, '.', '');
        $cartConditions = \Cart::getConditionsByType('giftcard');

        return view('menu.display', ['products' => $products, 'subtotal' => $cartSubTotal, 'total' => $cartTotal, 'cartConditions' => $cartConditions]);
    }

    public function updateCart(Request $request)
    {
        $data = $request->input();
        $product = $data['product'];
        $variantId = $product['variantId'];
        $quantity = $product['quantity'];

        \Cart::update($variantId, array(
            'quantity' => array(
                'relative' => false,
                'value' => $quantity
            ),
        ));

        app(CreditService::class)->applyCredits();

        $cartSubTotal = \Cart::getSubTotal();
        $cartTotalQuantity = \Cart::getTotalQuantity();
        $cartTotal = \Cart::getTotal();
        $creditCondition = \Cart::getConditionsByType('credits')->first();



        return response()->json([
            'subtotal' => $cartSubTotal,
            'quantity' => (int) $cartTotalQuantity,
            'total' => $cartTotal,
            'creditCondition' => $creditCondition
        ]);

    }
    public function removeFromCart(Request $request)
    {
        $data = $request->input('id');
        \Cart::remove($data);
        app(CreditService::class)->applyCredits();
        $cartSubTotal = \Cart::getSubTotal();
        $cartTotalQuantity = \Cart::getTotalQuantity();
        $cartTotal = \Cart::getTotal();
        $creditCondition = \Cart::getConditionsByType('credits')->first();

        return response()->json([
            'subtotal' => $cartSubTotal,
            'quantity' => (int) $cartTotalQuantity,
            'total' => $cartTotal,
            'creditCondition' => $creditCondition
        ]);

    }


    public function addToCart(Request $request)
    {
        $data = $request->input();
        $product = $data['product'];
        $variantId = $product['variantId'];
        $size = $product['productSize'];
        $price = $product['productPrice'];
        $name = $product['productName'];
        $image = $product['image'];
//        dd($image);
        $quantity = $product['quantity'];


        \Cart::add([
            'id'       => $variantId,
            'name'     => $name,
            'price'    => $price,
            'quantity' => $quantity,
            'attributes' => array(
                'size' => $size,
                'image' => $image,
                'shipping_address' => '',
            )
        ]);
        $cartSubTotal = \Cart::getSubTotal();
        $cartTotal = \Cart::getTotal();
        $cartTotalQuantity = \Cart::getTotalQuantity();
        $creditCondition = \Cart::getConditionsByType('credits')->first();

        return response()->json([
            'subtotal' => $cartSubTotal,
            'quantity' => (int) $cartTotalQuantity,
            'total' => $cartTotal,
            'creditCondition' => $creditCondition
        ]);

    }
}
