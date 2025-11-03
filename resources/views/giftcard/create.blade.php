@extends('layout.app')

@section('title', 'Menu')

@section('content')
    <form action="{{route('giftcards.store')}}" id="giftCardForm" method="post" class="p-4 bg-light rounded shadow-sm">

        @csrf

        <div class="card border-0 shadow-sm p-4 mb-4">
            <h5 class="text-center fw-semibold text-primary mb-4">Create GiftCard</h5>

            <!-- User Selection -->
            <div class="mb-4">
                <label for="user_id" class="form-label fw-semibold">Select User</label>
                <select id="user_id" name="user_id" class="form-select" required>
                    <option value="anyone">Select this to let anyone use this card</option>
                    @foreach($users as $user)
                        <option value="{{$user->id}}">{{$user->first_name}} {{$user->last_name}}</option>
                    @endforeach
                </select>
                @error('user_id')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <!-- Code and Balance -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="code" class="form-label fw-semibold">Code</label>
                    <input class="input form-control" type="number" name="code" id="code" value="{{ old('code') }}"
                           placeholder="Enter Code" step="1">
                    @error('code')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6">
                    <label for="balance" class="form-label fw-semibold">Balance</label>
                    <input class="input form-control" type="text" name="balance" id="balance" value="{{ old('balance') }}"
                           min="0" placeholder="Enter Discount Value" >
                    @error('balance')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Expiry and Notes -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="expiry_date" class="form-label fw-semibold">Expiry</label>
                    <input type="date" id="expiry_date" name="expiry_date" class="date form-control"  value="{{ old('expiry_date') }}" >
                    @error('expiry_date')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror

                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold d-block mb-2">Status</label>
                    <div class="form-check form-switch d-flex align-items-center">
                        <input class="form-check-input" type="checkbox" role="switch" id="statusToggle" name="status" checked  value="{{ old('status') }}" >
                        @error('status')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                        <label class="form-check-label ms-2" for="statusToggle" id="statusLabel">Enabled</label>
                    </div>
                    <input type="hidden" name="status" id="status" value="1">
                </div>
            </div>

            <!-- Submit -->
            <div class="d-flex justify-content-center">
                <button type="submit" id="submit" class="button btn btn-primary px-5 py-2 fw-semibold">
                    Submit
                </button>
            </div>
        </div>
    </form>


    @vite(['resources/js/giftcard.js'])
@endsection
