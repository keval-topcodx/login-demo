<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Laravel')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/menu.css'])
</head>
<body class="bg-light p-4">
<nav class="navbar bg-light border-bottom px-4">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        <div>
            @php
                $isMenuPage = Route::currentRouteName() == 'menu.index';
            @endphp

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

        </div>
        <div class="d-flex align-items-center">
            <div class="dropdown d-inline">
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



@yield('content')

</body>
</html>
