@extends('layout.app')

@section('title', 'Create Page')

@section('content')
    <div class="container form-section mt-5">
        <h2 class="mb-4">Create User</h2>

        <form id="userForm" action="{{route('users.store')}}" method="POST" novalidate enctype="multipart/form-data">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="first_name" class="form-label">First name</label>
                    <input type="text" id="first_name" name="first_name" class="form-control" value="{{ old('first_name') }}">
                    @error('first_name')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="last_name" class="form-label">Last name</label>
                    <input type="text" id="last_name" name="last_name" class="form-control" value="{{ old('last_name') }}">
                    @error('last_name')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="my-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control"  value="{{ old('email') }}">
                @error('email')
                <div class="text-danger">{{ $message }}</div>
                @enderror

            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control">
                    @error('password')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">

                </div>
            </div>

            <div class="my-3">
                <label for="phone_no" class="form-label">Phone Number</label>
                <input type="text" maxlength="10" pattern="[0-9]{10}" id="phone_no" name="phone_no" class="form-control"  value="{{ old('phone_no') }}">
                @error('phone_no')
                <div class="text-danger">{{ $message }}</div>
                @enderror

            </div>

            <hr class="my-4">

            <div class="mb-3">
                <label class="form-label d-block">Hobbies</label>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="hobbies[]" id="hobby_reading" value="reading"
                        {{ in_array('reading', old('hobbies', [])) ? 'checked' : '' }}>
                    <label class="form-check-label" for="hobby_reading">Reading</label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="hobbies[]" id="hobby_sports" value="sports"
                        {{ in_array('sports', old('hobbies', [])) ? 'checked' : '' }}>
                    <label class="form-check-label" for="hobby_sports">Sports</label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="hobbies[]" id="hobby_music" value="music"
                        {{ in_array('music', old('hobbies', [])) ? 'checked' : '' }}>
                    <label class="form-check-label" for="hobby_music">Music</label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="hobbies[]" id="hobby_travel" value="travel"
                        {{ in_array('travel', old('hobbies', [])) ? 'checked' : '' }}>
                    <label class="form-check-label" for="hobby_travel">Travel</label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="hobbies[]" id="hobby_coding" value="coding"
                        {{ in_array('coding', old('hobbies', [])) ? 'checked' : '' }}>
                    <label class="form-check-label" for="hobby_coding">Coding</label>
                </div>

                @error('hobbies')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>



            <div class="mb-3">
                <label class="form-label d-block">Gender</label>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="gender_male" value="male"
                        {{ old('gender') == 'male' ? 'checked' : '' }}>
                    <label class="form-check-label" for="gender_male">Male</label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="gender_female" value="female"
                        {{ old('gender') == 'female' ? 'checked' : '' }}>
                    <label class="form-check-label" for="gender_female">Female</label>
                </div>

                @error('gender')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="my-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" id="image" name="image" class="form-control"  value="{{ old('image') }}">
                @error('image')
                <div class="text-danger">{{ $message }}</div>
                @enderror

            </div>


            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Submit</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
        </form>
    </div>
@endsection
