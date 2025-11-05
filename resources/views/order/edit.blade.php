@extends('layout.app')

@section('title', 'Edit Page')

@section('content')
    <div class="container form-section mt-5">
        <h2 class="mb-4" id="orderId" data-id="{{$order->id}}">Edit Order - {{ $order->id }}</h2>



        <div class="row">
            <!-- CART ITEMS SECTION -->
            <div class="col-md-8">

                <form class="d-flex mb-5 gap-3" id="searchProductForm" novalidate>

                    @csrf
{{--                    @method('POST')--}}
                    <input class="form-control" type="text" name="search" id="search" placeholder="Enter Product Title" autocomplete="off">
                    <button type="submit" class="btn btn-primary">Browse</button>

                </form>

                <div class="item-container border rounded p-5 shadow-sm bg-white">
                    @foreach($order->items as $item)
                        <div class="item-card d-flex align-items-center justify-content-between py-3 border-bottom"
                             data-variant="{{ $item['variant_id'] }}"
                             data-size="{{ $item->variant->title }}"
                             data-price="{{ $item->variant->price }}"
                             data-name="{{ $item->variant->product->title }}"
                             data-image="{{ $item->variant->product->image_urls[0] }}">

                            <!-- IMAGE -->
                            <div class="item-image me-3 flex-shrink-0">
                                <img src="{{ $item->variant->product->image_urls[0] }}"
                                     class="order-item-image img-fluid rounded"
                                     alt="food image"
                                     style="width: 80px; height: 80px; object-fit: cover;">
                            </div>

                            <!-- ITEM DETAILS -->
                            <div class="order-item-details flex-grow-1">
                                <p class="order-item-name fw-semibold mb-1">{{ $item->variant->product->title }}</p>

                                <div class="size-details d-flex align-items-center mb-2">
                                    <p class="mb-0 me-2">Size:</p>
                                    <p class="order-item-size mb-0 fw-medium">{{ $item->variant->title }}</p>
                                </div>

                                <div class="add-quantity-button" style="max-width: 100px;">
                                    <input type="number"
                                           class="form-control order-quantity text-center"
                                           value="{{ $item->quantity }}"
                                           min="0"
                                           required>
                                </div>
                            </div>

                            <!-- PRICE + REMOVE -->
                            <div class="order-price-details text-end ms-3">
                                <button class="btn btn-outline-danger btn-sm d-inline-flex justify-content-center align-items-center p-0 remove-item-button ms-auto"
                                        style="width: 30px; height: 30px; line-height: 0;">
                                    ×
                                </button>
                                <p class="order-item-price fw-bold mb-0 mt-2">${{ number_format(($item->quantity * $item->price), 2, '.', '') }}</p>
                            </div>

                        </div>

                    @endforeach
                </div>
                <div class="bg-white mt-5 order-payment-summary p-5 border rounded shadow-sm">
                    <h4>Payment</h4>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <span class="subtotal">${{$order->subtotal}}</span>
                    </div>

                    @foreach($order->discounts as $discount)
                        @if($discount && $discount->name)
                            <div class="d-flex justify-content-between mb-2">
                                <span>{{$discount->name}}</span>
                                <span class="discount" data-discount="{{$discount->amount}}">-${{number_format(abs($discount->amount), 2)}}</span>
                            </div>
                        @endif
                    @endforeach

                    <div class="d-flex justify-content-between mb-2">
                        <span>Total</span>
                        <span class="total">
                            @if($order->discount && $order->discount->name)
                                ${{$order->subtotal + $order->discount->amount}}
                            @else
                                ${{$order->subtotal}}
                            @endif
                        </span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between fw-bold mb-3">
                        <span>Paid By Customer</span>
                        <span class="paid-by-customer" data-amount-paid="{{$order->amount_paid}}">${{$order->amount_paid}}</span>
                    </div>
                </div>

            </div>

            <!-- SUMMARY SECTION -->
            <div class="col-md-4">
                <div class="order-summary border rounded p-4 shadow-sm bg-light sticky-top" style="top: 80px;">
                    <h5 class="fw-semibold mb-3">Order Summary</h5>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Updated total</span>
                        <span class="total">
                            @if($order->discount && $order->discount->name)
                                ${{$order->subtotal + $order->discount->amount}}
                            @else
                                ${{$order->subtotal}}
                            @endif
                        </span>
                    </div>

                    <div class="d-flex justify-content-between mb-3">
                        <span>Paid by customer</span>
                        <span class="paid-by-customer">${{$order->amount_paid}}</span>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between fw-bold mb-3">
                        <span>Amount to collect</span>
                        <span class="amount-to-collect">$0</span>
                    </div>

                    <hr>

                    <button id="updateOrder" class="btn btn-primary w-100">Update Order</button>
                </div>
            </div>
        </div>
    </div>
    <!-- PRODUCT SEARCH MODAL -->
    <div id="productModal" class="product-modal d-none">
        <div class="product-modal-content rounded shadow">
            <button class="btn-close position-absolute top-0 end-0 m-3" id="closeModal"></button>
            <h4 class="mb-3">Search Results</h4>
            <div class="search-products">
                <input class="form-control mb-3" type="text" name="searchProductsInput" id="searchProductsInput" placeholder="Enter Product Title" autocomplete="off">
            </div>
            <form id="addToOrderForm" novalidate>
                @csrf
                <div id="productResults">

                </div>
                <span class="text-danger small add-order-error"></span>
                <button type="submit" name="submit" class="btn btn-primary w-100 mt-3" id="addSelectedProductsButton">ADD</button>
            </form>
        </div>
    </div>

    @vite(['resources/js/order.js'])
@endsection
