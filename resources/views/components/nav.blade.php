<nav class="navbar bg-light border-bottom px-4">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div>
            <span class="navbar-brand fw-bold">Menu</span>
            <a href="{{ route('dashboard') }}" class="text-decoration-none me-3 text-black">Dashboard</a>
            <a href="{{ route('menu.index') }}" class="text-decoration-none me-3 text-black">Menu</a>
            <a href="{{ route('giftcards.index') }}" class="text-decoration-none me-3 text-black">Gift-cards</a>
        </div>
        <div class="d-flex align-items-center">
            <span class="me-3 text-muted">Hello, <strong>{{ auth()->user()->first_name }}</strong></span>
            <a class="btn btn-outline-danger btn-sm" id="cartButton">CART</a>
        </div>
    </div>
    <div class="cart-page" id="cartPage">
        <div class="cart-section">
            <div class="cart">
                <div class="section-header">
                    <h4 class="section-heading">Your Cart</h4>
                    <button class="hide-cart-button" id="hideCartPage">X</button>
                </div>
                <hr>
                <div class="cart-container">
                    <!-- cart items go here -->
                </div>
                <div class="order-price">
                    <a href=" {{route('order.index')}} " class="order-price-button btn btn-success">FINAL BILL $00.00</a>
                </div>
            </div>
        </div>
    </div>
    <div class="overlay"></div>
</nav>
