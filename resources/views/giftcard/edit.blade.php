@extends('layout.app')

@section('title', 'Menu')

@section('content')

    <form action="{{route('giftcards.update', $giftcard)}}" id="giftCardForm" method="POST" class="p-4 bg-light rounded shadow-sm">

        @csrf
        @method('PUT')

        <div class="card border-0 shadow-sm p-4">
            <h5 class="mb-4 text-center fw-semibold text-primary">Edit GiftCard</h5>
            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label for="code" class="form-label fw-semibold">Code</label>
                    <input class="input form-control" type="number" name="code" id="code" value="{{$giftcard->code}}"
                           readonly>
                    @error('code')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="initial_balance" class="form-label fw-semibold">Initial Balance</label>
                    <input class="input form-control" type="number" name="initial_balance" id="initial_balance" value="{{$giftcard->initial_balance}}"
                           readonly>
                    @error('initial_balance')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6 mb-3">
                    <label for="balance" class="form-label fw-semibold">Balance</label>
                    <input class="input form-control" type="text" name="balance" id="balance" value="{{$giftcard->balance}}"
                           min="0">
                    @error('balance')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3 align-items-center">
                <div class="col-md-6 mb-3">
                    <div id="expDateInput">
                        <label for="expiry_date" class="form-label fw-semibold">Expiry</label>
                        <input type="date" id="expiry_date" name="expiry_date" class="date form-control" value="{{$giftcard->expiry_date}}">
                        @error('expiry_date')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold d-block mb-2">Status</label>
                    <div class="form-check form-switch d-flex align-items-center">
                        <input class="form-check-input" type="checkbox" role="switch" id="statusToggle" checked>
                        <label class="form-check-label ms-2" for="statusToggle" id="statusLabel">Enabled</label>
                        @error('status')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <input type="hidden" name="status" id="status" value="1">
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-4">
            <button type="submit" id="submit" class="button btn btn-primary px-5 py-2 fw-semibold">
                Submit
            </button>
        </div>
    </form>



    @vite(['resources/js/giftcard.js'])
@endsection
