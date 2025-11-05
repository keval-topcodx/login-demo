@extends('layout.app')

@section('title', 'Menu')

@section('cart-button')
    @include('menu.cart')
@endsection

@section('content')
@if(request('message'))
    <div class="alert alert-success">
        {{ request('message') }}
    </div>
@endif
@if (session('danger'))
    <div class="alert alert-danger">
        {{ session('danger') }}
    </div>
@endif
<div class="container my-5">
    <div class="border rounded-4 p-4 shadow-sm bg-white">
        <h3 class="mb-4 text-center">This Week's Meals

        </h3>

        <div class="row g-4 gap-3 d-flex m-auto" id="cardContainer">
            @foreach($products as $product)
                <div class="card gap-3"
                     style="width: 18rem;"
                     data-id="{{$product->id}}"
                     data-selected-variant="{{$product->variants->pluck('id')->first()}}"
                     data-selected-size="{{$product->variants->pluck('title')->first()}}"
                     data-selected-price="{{$product->variants->pluck('price')->first()}}"
                     data-name="{{$product->title}}"
                     data-image="{{ $product->image_urls[0] }}"
                >
                    <img
                        src="{{ $product->image_urls[0] ?? asset('images/no-image.jpg') }}"
                        class="card-img-top rounded"
                        style="object-fit: cover; height: 250px;"
                        alt="Product image">

                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{$product->title}}</h5>
                        <div class="size-container d-flex gap-3">
                        @foreach($product->variants as $variant)
                            <button class="btn btn-light size-buttons" data-price="{{$variant->price}}" data-size="{{$variant->title}}" data-variant-id="{{$variant->id}}">{{ucfirst($variant->title[0])}}</button>
                        @endforeach
                        </div>
                        <div class="d-flex align-items-center mt-3 justify-content-between">
                            <p class="mb-0 product-price">${{ $product->variants->pluck('price')->first() }}</p>
                            <div class="add-button ms-auto d-flex justify-content-end">


                                @php
                                    $cartCollection = \Cart::getContent()->toArray();
                                    $alreadyInCart = false;
                                    $quantity = 0;
                                    $variantId = $product->variants->pluck('id')->first();

                                    foreach ($cartCollection as $item) {
                                        if ((int) $item['id'] === (int) $variantId) {
                                            $alreadyInCart = true;
                                            $quantity = $item['quantity'];
                                            break;
                                        }
                                    }
                                @endphp

                                @if ($alreadyInCart)
                                    <input type="number"
                                           class="form-control cart-quantity ms-auto d-flex justify-content-center align-items-center"
                                           value="{{ $quantity }}"
                                           min="0"
                                           required>
                                @else
                                    <a class="btn btn-primary px-3 py-1 d-flex justify-content-center align-items-center fw-bold add-to-cart-button">
                                        Add >
                                    </a>
                                @endif

                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

    @vite(['resources/js/cart.js'])
@endsection
