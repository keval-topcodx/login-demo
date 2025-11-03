@extends('layout.app')

@section('title', 'Payment Successful')

@section('content')
    <div class="container py-5 d-flex justify-content-center align-items-center" style="min-height: 80vh;">
        <div class="card shadow-lg border-success text-center" style="max-width: 500px;">
            <div class="card-body p-5">
                <div class="mb-4">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                </div>
                <h3 class="text-success mb-3">Payment Successful!</h3>
                <p class="text-muted mb-4">
                    Thank you for your purchase. Your payment has been successfully processed.
                </p>
                <p class="text-muted mb-4">
                    Payment Id: {{ request('payment_intent') }}
                </p>
                <a href="{{ route('menu.index') }}" class="btn btn-success">
                    <i class="bi bi-house me-2"></i>Return to Home
                </a>
            </div>
        </div>
    </div>
@endsection
