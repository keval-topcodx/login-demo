@extends('layout.app')

@section('title', 'Dashboard Page')

@section('content')

    <div class="container-fluid">
        @if (session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
        @endif
    </div>
    <!-- Main Content -->
    <div class="col py-3">
        <nav class="navbar bg-white border-bottom mb-4">
            <div class="p-3">
                <span class="navbar-brand mb-0 h5">Hello {{auth()->user()->first_name}}, Your Email is not verified</span>
            </div>
        </nav>

        <div class="container">
            <div class="card">
                <div class="card-body">
                    <p class="card-text text-muted">We have sent the verification mail to {{auth()->user()->email}}. If you can not find the email verification mail in the index folder.
                    Please check Junk/Spam folder.</p>
                    <p class="card-text text-muted">If you did not receive the email verification mail. please click resend button.</p>

                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <div class="d-flex align-items-center justify-content-center">
                            <button class="btn btn-primary" type="submit">
                                Resend Verification Email
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
