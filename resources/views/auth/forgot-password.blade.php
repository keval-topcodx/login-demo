@extends('layout.app')

@section('title', 'Login Page')

@section('content')
    <div class="container-fluid">
        @if (session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif
    </div>
    <div class="container form-section card p-5 w-25 pb-3">
        <h3 class="mb-4 text-center">Forgot Password</h3>

        <form id="userForm" action="{{route('password.email')}}" method="POST" novalidate>

            @csrf
            <div class="my-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control"  value="{{ old('email') }}">
                @error('email')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2 mt-4 row">
                <button type="submit" class="btn btn-primary">Send Reset Password Link</button>
            </div>
        </form>
    </div>
@endsection
