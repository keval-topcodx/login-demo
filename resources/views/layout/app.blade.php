<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Laravel')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/menu.css', 'resources/js/notification.js'])
</head>
<body class="bg-light p-4">
@php
    $isMenuPage = Route::currentRouteName() == 'menu.index';
    $isCheckoutPage = Route::currentRouteName() == 'checkout';
@endphp
@unless($isCheckoutPage)
<nav class="navbar bg-light border-bottom px-4">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div>



            <a href="{{ route('dashboard') }}" class="text-decoration-none me-3 text-black">Dashboard</a>
            <a href="{{ route('menu.index') }}" class="text-decoration-none me-3 text-black">Menu</a>
            <a href="{{ route('user-orders.index') }}" class="text-decoration-none me-3 text-black">Orders</a>
            @role('admin')
            @unless($isMenuPage)
                <a href="{{ route('users.index') }}" class="text-decoration-none me-3 text-black">Users</a>
                <a href="{{ route('giftcards.index') }}" class="text-decoration-none me-3 text-black">GiftCards</a>
                <a href="{{ route('products.index') }}" class="text-decoration-none me-3 text-black">Products</a>
                <a href="{{ route('roles.index') }}" class="text-decoration-none me-3 text-black">Roles</a>
                <a href="{{ route('permissions.index') }}" class="text-decoration-none me-3 text-black">Permissions</a>
            @endunless
            @endrole

            @hasanyrole(['admin', 'agent'])
            <a href="{{ route('chat.index') }}" class="text-decoration-none me-3 text-black">Chat</a>
            @endhasanyrole
        </div>
        <div class="d-flex align-items-center">
            <div class="dropdown d-inline">
                @hasanyrole(['admin', 'agent'])

                <div class="position-relative d-inline-block">
                    <!-- Notification Button -->
                    <button type="button" class="btn btn-light border position-relative" id="notifications">
                        &#128229;
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="unreadCount">
                        </span>
                    </button>

                    <!-- Notification Dropdown -->
                    <div id="notificationPanel" class="notification-panel shadow-lg rounded d-none">
                        <div class="p-2 border-bottom bg-light d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">Notifications</span>
                            <button class="btn btn-sm btn-outline-secondary py-0" id="clearNotifications">Clear</button>
                        </div>

                        <div class="notification-list">
                            <!-- Example notifications -->
{{--                            <div class="notification-item">--}}
{{--                                <strong>John Doe</strong><br>--}}
{{--                                <span class="text-muted small">Hello, I need help.</span>--}}
{{--                            </div>--}}
{{--                            <div class="notification-item unread">--}}
{{--                                <strong>Jane Smith</strong><br>--}}
{{--                                <span class="text-muted small">📎 File: invoice.pdf</span>--}}
{{--                            </div>--}}
{{--                            <div class="notification-item">--}}
{{--                                <strong>Ravi Patel</strong><br>--}}
{{--                                <span class="text-muted small">🖼️ Image received</span>--}}
{{--                            </div>--}}
                        </div>
                    </div>
                </div>


                @endhasanyrole
                <span class="me-3 text-muted dropdown-toggle" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Hello, <strong>{{ auth()->user()->first_name }}</strong>
                </span>
                <ul class="dropdown-menu" aria-labelledby="userDropdown">
                    <li>
                        <form action="{{ route('logout') }}" method="GET" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>


            @yield('cart-button')
        </div>
    </div>
</nav>
@endunless



@yield('content')
</body>
</html>
