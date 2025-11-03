@extends('layout.app')

@section('title', 'Payment Cancelled')

@section('content')
    <div class="container py-5 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="card shadow-lg border-danger text-center" style="max-width: 500px;">
            <div class="card-body p-5">
                <div class="mb-4">
                    <i class="bi bi-x-circle-fill text-danger" style="font-size: 4rem;"></i>
                </div>
                <h3 class="text-danger mb-3">Payment Cancelled</h3>
                <p class="text-muted mb-4">
                    Your payment was not completed. You can try again or contact support if the issue persists.
                </p>
                <a href="{{ route('checkout') }}" class="btn btn-outline-danger">
                    <i class="bi bi-arrow-left me-2"></i>Try Again
                </a>
            </div>
        </div>
    </div>
@endsection
