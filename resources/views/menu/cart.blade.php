<a class="btn btn-outline-danger btn-sm" id="cartButton">CART</a>

<div class="cart-page" id="cartPage">
    <div class="cart-section">
        <div class="cart">
            <div class="section-header">
                <h4 class="section-heading">Your Cart</h4>
                <button class="hide-cart-button" id="hideCartPage">X</button>
            </div>
            <hr>
            <div class="cart-container">
{{--             cart items go here--}}
            </div>
{{--            giftcard container--}}
            <hr class="mt-auto">
            <div class="p-3 bg-info-subtle rounded shadow-sm order-details-container">
                <div class="giftcard-container mb-4">
                    <form id="applyGiftCardForm" method="post" class="row g-2 align-items-end">
                        @csrf
                        <div class="col-12">
                            <label for="giftCode" class="form-label mb-1 fw-semibold">Gift Card</label>
                        </div>
                        <div class="col-8">
                            <input
                                class="giftcard-input form-control"
                                type="number"
                                id="giftCode"
                                name="giftCode"
                                placeholder="Enter Gift Card Code"
                                min="0"
                                step="1"
                                value="25155122"
                            >
                        </div>
                        <div class="col-4 d-grid">
                            <button
                                type="submit"
                                id="applyGiftCard"
                                class="apply-button btn btn-outline-primary"
                            >
                                APPLY
                            </button>
                        </div>
                        <div class="col-12">
                <span
                    class="giftcard-error-message text-danger small d-block mt-1"
                    style="min-height: 1em;"
                ></span>
                        </div>
                    </form>
                </div>

                <hr class="my-3">


                <div class="order-price-container">
                    <div class="order-price-info d-flex justify-content-between mb-2 small text-muted">
                        <span>Subtotal</span>
                        <span class="subtotal fw-semibold text-dark">${{$subtotal}}</span>
                    </div>
                    <div class="order-giftcard-info d-flex justify-content-between mb-2 small text-muted">
                        @if($cartConditions->isNotEmpty())
                            @foreach($cartConditions as $condition)
                                <span>{{ $condition->getType() }} - {{ $condition->getName() }}
                                <a class="remove-giftcard btn btn-danger btn-sm p-1 ms-3" style="font-size: 0.75rem; line-height: 1;">X</a>
                                </span>
                                <span class="discounted-value fw-semibold text-dark">{{ $condition->getValue() }}</span>
                            @endforeach
                        @endif
                    </div>

                    <div class="order-total d-flex justify-content-between border-top pt-2 mt-3">
                        <span class="fw-bold fs-6">Total</span>
                        <span class="total fw-bold fs-6">${{$total}}</span>
                    </div>
                </div>
            </div>

            <hr>

            <div class="order-price">
                <a href="{{ route('order.index') }}" class="order-price-button btn btn-info text-info-emphasis">
                    CHECKOUT
                </a>
            </div>
        </div>
    </div>
</div>
<div class="overlay"></div>
