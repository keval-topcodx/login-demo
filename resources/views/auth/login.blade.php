<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/menu.css'])
</head>
<body class="bg-light">

    <div class="container form-section card p-5 w-25 pb-3 mt-5">
        <h2 class="mb-4 text-center">Login</h2>

        <form id="userForm" action="{{route('login.process')}}" method="POST" novalidate>

            @csrf
            <div class="my-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control"  value="{{ old('email') }}">
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
            <div class="">
                <p class="mb-0">
                    <a href="{{ route('password.request') }}"
                       class="text-secondary text-decoration-none"
                       onmouseover="this.classList.add('text-dark')"
                       onmouseout="this.classList.remove('text-dark')">
                        Forgot Password?
                    </a>
                </p>

            </div>

            <div class="d-flex gap-2 mt-4 row">
                <button type="submit" class="btn btn-primary">Login</button>
                {{--                <button type="reset" class="btn btn-secondary">Reset</button>--}}
            </div>
            <div class="row d-flex align-items-center justify-content-center mt-4 text-black-50">
                <p>Don't have an account? <a href="{{route('users.create')}}" class="text-decoration-none">Register Here</a></p>
            </div>
        </form>
    </div>
</body>
</html>
