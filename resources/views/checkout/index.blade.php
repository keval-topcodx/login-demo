@extends('layout.app')

@section('title', 'Checkout Page')

@section('content')
    <form id="payment-form">
        @csrf
        <input id="card-holder-name" type="text">
        <div id="card-element"></div>
        <button id="card-button" class="btn btn-primary mt-5" data-secret="{{ $clientSecret }}">
            Checkout
        </button>
    </form>


    <script src="https://js.stripe.com/v3/"></script>
    <script>
        const stripe = Stripe('{{ env('STRIPE_KEY') }}');
        const elements = stripe.elements({ clientSecret: '{{ $clientSecret }}' });

        const paymentElement = elements.create('payment');
        paymentElement.mount('#card-element');

        window.checkoutSuccessUrl = "{{ route('menu.index') }}";

    </script>

    @vite(['resources/js/checkout.js'])
@endsection
