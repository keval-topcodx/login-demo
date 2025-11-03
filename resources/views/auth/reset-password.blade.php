@extends('layout.app')

@section('title', 'Reset Password')

@section('content')
    <div>
        {{$token}}
    </div>
    <div class="container form-section card p-5 w-25 pb-3">
        <h2 class="mb-4 text-center">Reset Password</h2>

        <form id="userForm" action="{{route('password.update')}}" method="POST" novalidate>

            @csrf
            <div class="my-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control"
                       value="{{ $email }}" readonly>
                @error('email')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="my-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control">
                @error('password')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="my-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">

            </div>

            <div class="d-flex gap-2 mt-4 row">
                <button type="submit" class="btn btn-primary">Reset Password</button>
            </div>
        </form>
    </div>
@endsection
