@extends('layout.app')

@section('title', 'Dashboard Page')

@section('content')
    <!-- Navbar -->
{{--    <nav class="navbar bg-light border-bottom px-4">--}}
{{--        <div class="container-fluid d-flex justify-content-between align-items-center">--}}
{{--            <div>--}}
{{--                <span class="navbar-brand fw-bold">My Dashboard</span>--}}
{{--                <a href="{{ route('users.index') }}" class="text-decoration-none me-3 text-black">Users</a>--}}
{{--                <a href="{{ route('products.index') }}" class="text-decoration-none me-3 text-black">Products</a>--}}
{{--                <a href="{{ route('menu.index') }}" class="text-decoration-none me-3 text-black">Menu</a>--}}
{{--            </div>--}}
{{--            <div class="d-flex align-items-center">--}}
{{--                <span class="me-3 text-muted">Welcome, <strong>{{ auth()->user()->first_name }}</strong></span>--}}
{{--                <a href="{{ route('logout') }}" class="btn btn-outline-danger btn-sm">Log Out</a>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </nav>--}}

    <!-- Main Content -->
    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-body">
                <h5 class="card-title mb-3">Dashboard</h5>
                <p class="card-text text-muted">
                    This is your dashboard. You can start adding content here.
                </p>
            </div>
        </div>
    </div>
@endsection
