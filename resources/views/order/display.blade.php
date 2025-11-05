@extends('layout.app')

@section('title', 'Menu')

@section('content')
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('danger'))
        <div class="alert alert-danger">
            {{ session('danger') }}
        </div>
    @endif

    <section class="main-section container my-5">
        <div class="row g-4">
            <!-- LEFT SIDE -->
            <div class="col-md-6">
                <div class="shipping-card card p-4 shadow-sm">
                    <h3 class="mb-4">Shipping Details</h3>
                    <hr>
                    <form id="orderForm" action="{{route('order.store')}}" class="form" id="shippingForm" method="POST">

                        @csrf
                        @method('POST')


                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" name="first_name" id="first_name" value="{{ old('first_name', auth()->user()->shipping_address['first_name'] ?? '') }}">
                            @error('first_name')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="last_name" id="last_name" value="{{ old('last_name', auth()->user()->shipping_address['last_name'] ?? '') }}">
                            @error('last_name')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="street_address" class="form-label">Street Address</label>
                            <input type="text" class="form-control" name="street_address" id="street_address" value="{{ old('street_address', auth()->user()->shipping_address['street_address'] ?? '') }}">
                            @error('street_address')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control" name="city" id="city" value="{{ old('city', auth()->user()->shipping_address['city'] ?? '') }}">
                            @error('city')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="country" class="form-label">Country</label>
                                <select name="country" id="country" class="form-select">
                                    <option value="india" selected>India</option>
                                </select>
                                @error('country')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="state" class="form-label">State</label>
                                <select name="state" id="state" class="form-select">
                                    <option value="surat"
                                        {{ old('state', auth()->user()->shipping_address['state'] ?? '') == 'surat' ? 'selected' : '' }}>
                                        Surat
                                    </option>
                                    <option value="mumbai"
                                        {{ old('state', auth()->user()->shipping_address['state'] ?? '') == 'mumbai' ? 'selected' : '' }}>
                                        Mumbai
                                    </option>
                                    <option value="baroda"
                                        {{ old('state', auth()->user()->shipping_address['state'] ?? '') == 'baroda' ? 'selected' : '' }}>
                                        Baroda
                                    </option>
                                    <option value="pune"
                                        {{ old('state', auth()->user()->shipping_address['state'] ?? '') == 'pune' ? 'selected' : '' }}>
                                        Pune
                                    </option>
                                </select>
                                @error('state')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="postcode" class="form-label">Post Code</label>
                            <input type="number" class="form-control" name="postcode" id="postcode" step="1" min="0" value="{{ old('postcode', auth()->user()->shipping_address['postcode'] ?? '') }}">
                            @error('postcode')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" id="submit" class="create-order-button btn btn-primary">
                                Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- RIGHT SIDE -->
            <div class="col-md-6">
                <div class="orders-items-summary card p-4 shadow-sm">
                    <div class="section-header mb-3">
                        <h3 class="header">Order Summary</h3>
                    </div>
                    <hr>

                    <div class="cart-container mb-3">
{{--                        Order items--}}
                        @foreach($cartItems as $item)
                            <div class="cart-card d-flex align-items-center justify-content-between border rounded p-3 mb-3 shadow-sm"
                                 data-variant="{{$item['id']}}"
                                 data-size="{{$item['attributes']['size']}}"
                                 data-price="{{$item['price']}}"
                                 data-name="{{$item['name']}}"
                                 data-image="{{json_encode($item['attributes']['image'])}}">

                                <!-- IMAGE -->
                                <div class="cart-image me-3 flex-shrink-0">
                                    <img src="{{$item['attributes']['image']}}"
                                         class="cart-item-image img-fluid rounded"
                                         alt="food image"
                                         style="width: 80px; height: 80px; object-fit: cover;">
                                </div>

                                <!-- ITEM DETAILS -->
                                <div class="cart-item-details flex-grow-1">
                                    <p class="cart-item-name fw-semibold mb-1">{{$item['name']}}</p>

                                    <div class="size-details d-flex align-items-center mb-2" data-size="{{$item['attributes']['size']}}">
                                        <p class="mb-0 me-2">Size:</p>
                                        <p class="cart-item-size mb-0 fw-medium">{{$item['attributes']['size']}}</p>
                                    </div>

                                    <div class="add-quantity-button" style="max-width: 100px;">
                                        <input type="number"
                                               class="form-control cart-quantity text-center"
                                               value="{{$item['quantity']}}"
                                               min="0"
                                               data-id=""
                                               required>
                                    </div>
                                </div>

                                <!-- PRICE + REMOVE -->
                                <div class="cart-price-details text-end ms-3" data-price="">
                                    <button class="btn btn-outline-danger btn-sm d-inline-flex justify-content-center align-items-center p-0 remove-item-button ms-auto"
                                            style="width: 30px; height: 30px; line-height: 0;">
                                        ×
                                    </button>
                                    <p class="cart-item-price fw-bold mb-0 mt-2">${{number_format($item['price'], 2, '.', '')}}</p>

                                </div>
                            </div>

                        @endforeach
{{--                        Order items--}}
                    </div>

                    <hr>

                    <div class="bill-container" id="billContainer">
                        <div class="order-price-info d-flex justify-content-between mb-2 small">
                            <span>Subtotal</span>
                            <span class="subtotal fw-semibold text-dark">${{number_format($subtotal, 2, '.', '')}}</span>
                        </div>
                        <div class="order-giftcard-info d-flex justify-content-between mb-2 small text-muted">
                            @if($giftCardConditions->isNotEmpty())
                                @foreach($giftCardConditions as $condition)
                                    <span>{{ ucfirst($condition->getType()) }} - {{ $condition->getName() }}
                                        <a class="remove-giftcard btn btn-danger btn-sm p-1 ms-3" style="font-size: 0.75rem; line-height: 1;">X</a>
                                    </span>
                                    <span class="discounted-value fw-semibold text-dark">{{number_format($condition->getValue(), 2, '.', '')}}</span>
                                @endforeach
                            @endif
                        </div>

{{--                        user credits--}}
                        <div class="credits-used-info d-flex justify-content-between mb-2 small text-muted">
                            @if($creditConditions->isNotEmpty())
                                @foreach($creditConditions as $condition)
                                    <span>{{ auth()->user()->first_name . " " . auth()->user()->last_name}} - {{ $condition->getName() }}
                                        <a class="remove-giftcard btn btn-danger btn-sm p-1 ms-3" style="font-size: 0.75rem; line-height: 1;">X</a>
                                    </span>
                                    <span class="credits-used-value fw-semibold text-dark">{{number_format($condition->getValue(), 2, '.', '')}}</span>
                                @endforeach
                            @endif
                        </div>

{{--                        <div class="order-discountcode-info mb-2"></div>--}}
{{--                        <div class="order-giftcard-info mb-2"></div>--}}

                        <div class="order-total d-flex justify-content-between border-top pt-2 mt-3">
                            <p class="fw-bold">TOTAL</p>
                            <p class="total fw-bold">${{number_format($total, 2, '.', '')}}</p>
                        </div>

                        <hr>

                        <div class="discount-code-container mb-3">
                            <form id="codeForm" method="post" class="row g-2 align-items-end">
                                <div class="col-12 col-sm-8">
                                    <label for="code" class="form-label mb-1">Coupon Code</label>
                                    <input class="discount-input form-control" type="text" id="code" name="code" placeholder="Enter Discount Code">
                                    <span class="error-message text-danger small"></span>
                                </div>
                                <div class="col-12 col-sm-4 d-grid">
                                    <button type="submit" id="apply" class="apply-button btn btn-outline-primary">APPLY</button>
                                </div>
                            </form>
                        </div>

                        <div class="giftcard-container mb-3">
                            <form id="applyGiftCardForm" method="post" class="row g-2 align-items-end d-flex">
                                @csrf
                                <div class="col-12 col-sm-8">
                                    <label for="giftCode" class="form-label mb-1">Gift Card</label>
                                    <input class="giftcard-input form-control" type="number" id="giftCode" name="giftCode" placeholder="Enter Gift Card Code" min="0" step="1" value="25155122">

                                </div>
                                <div class="col-12 col-sm-4 d-grid mt-4">
                                    <button type="submit" id="applyGiftCard" class="apply-button btn btn-outline-primary">APPLY</button>
                                </div>
                                <span class="giftcard-error-message text-danger small d-block" style="min-height: 1em;"></span>
                            </form>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </section>



    @vite(['resources/js/cart.js'])
@endsection
