@extends('layout.app')

@section('title', 'Edit Page')

@section('content')
    <div class="container form-section mt-5">
        <h2 class="mb-4">Edit User</h2>

        <!-- Replace action with your server endpoint -->
        <form id="editUserForm" action="{{route('users.update', $user)}}" method="post" novalidate enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="first_name" class="form-label">First name</label>
                    <input type="text" id="first_name" name="first_name" class="form-control" value="{{$user->first_name}}" required>
                    @error('first_name')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="last_name" class="form-label">Last name</label>
                    <input type="text" id="last_name" name="last_name" class="form-control" value="{{$user->last_name}}"  required>
                    @error('last_name')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="my-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="{{$user->email}}" required>
                @error('email')
                <div class="text-danger">{{ $message }}</div>
                @enderror

            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" minlength="8" required>
                    @error('password')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="confirm_passwordpassword_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>

                </div>
            </div>

            <div class="my-3">
                <label for="phone_no" class="form-label">Phone Number</label>
                <input type="text" maxlength="10" pattern="[0-9]{10}" id="phone_no" name="phone_no" class="form-control"  value="{{ $user->phone_no }}">
                @error('phone_no')
                <div class="text-danger">{{ $message }}</div>
                @enderror

            </div>


            <hr class="my-4">

            <div class="mb-3">
                <label class="form-label d-block">Hobbies</label>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="hobbies[]" id="hobby_reading" value="reading" @checked(in_array('reading', json_decode($user->hobbies)))>
                    <label class="form-check-label" for="hobby_reading">Reading</label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="hobbies[]" id="hobby_sports" value="sports" @checked(in_array('sports', json_decode($user->hobbies)))>
                    <label class="form-check-label" for="hobby_sports">Sports</label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="hobbies[]" id="hobby_music" value="music" @checked(in_array('music', json_decode($user->hobbies)))>
                    <label class="form-check-label" for="hobby_music">Music</label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="hobbies[]" id="hobby_travel" value="travel" @checked(in_array('travel', json_decode($user->hobbies)))>
                    <label class="form-check-label" for="hobby_travel">Travel</label>
                </div>

                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="hobbies[]" id="hobby_coding" value="coding" @checked(in_array('coding', json_decode($user->hobbies)))>
                    <label class="form-check-label" for="hobby_coding">Coding</label>
                </div>
                @error('hobbies')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>


            <div class="mb-3">
                <label class="form-label d-block">Gender</label>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="gender_male" value="male"  @checked($user->gender == 'male') required>
                    <label class="form-check-label" for="gender_male">Male</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="gender" id="gender_female" value="female" @checked($user->gender == 'female') required>
                    <label class="form-check-label" for="gender_female">Female</label>
                </div>
                @error('gender')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="my-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" id="image" name="image" class="form-control">
                @error('image')
                <div class="text-danger">{{ $message }}</div>
                @enderror

            </div>
            <div class="d-flex flex-wrap gap-3 align-items-start justify-content-start overflow-hidden">
                @if(!empty($user->image_url))
                    <div class="col-md-3 mb-3">
                        <div class="card">
                            <img src="{{ $user->image_url }}" class="card-img-top rounded" style="object-fit: cover; height: 250px;" alt="Product image">
                        </div>
                    </div>
                @else
                    <p class="text-muted">No image available.</p>
                @endif

            </div>


            <div class="mb-4">
                <label class="form-label d-block mb-2">Select Roles</label>

                <div class="border rounded p-3 bg-white">
                    @foreach($roles as $role)
                        <div class="form-check mb-2">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="roles[]"
                                value="{{ $role->name }}"
                                id="{{ $role->name }}"
                                {{ in_array($role->name, old('roles', $user->roles->pluck('name')->toArray())) ? 'checked' : '' }}
                            >
                            <label class="form-check-label text-capitalize" for="{{ $role->name }}">
                                {{  $role->name }}
                            </label>
                        </div>
                    @endforeach

                </div>
{{--                products--}}
                <div class="container border rounded shadow-sm bg-white p-5 my-5">
                    <div class="d-flex justify-content-end mb-3">
                        <button type="button" class="add-product-btn btn btn-primary" id="addProductBtn">Add Product</button>
                    </div>
                    <table class="table table-bordered align-middle">
                        <thead>
                        <tr>
                            <th>NAME</th>
                            <th>VARIANT</th>
                            <th>PRICE</th>
                            <th>ACTION</th>
                        </tr>
                        </thead>
                        <tbody id="tableBody">
                        <tr class="table-row">
                            <td class="align-middle product-data">
                                <input type="text" class="form-control product-search-input" name="products[0][name]" placeholder="Enter Product Name" autocomplete="off">
                                @error('products[0][name]')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                                <div>
                                    <ul class="list-group" id="suggestedProducts" style="max-height: 200px; overflow-y: auto;">

                                    </ul>
                                </div>
                            </td>
                            <td class="align-middle">
                                <select name="products[0][variant]" class="form-select variant-select">
                                    <option value="" disabled >Choose variant</option>
                                </select>
                                @error('products[0][variant]')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </td>
                            <td class="align-middle">
                                <input type="number" name="products[0][price]" class="form-control variant-price" step="0.01" placeholder="0.00">
                                @error('products[0][price]')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </td>
                            <td class="text-center align-middle">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-variant" >Remove</button>
                            </td>
                        </tr>
                        @foreach(old('products', []) as $index => $product)
                            <tr class="table-row">
                                <td class="align-middle product-data">
                                    <input type="text" class="form-control product-search-input" name="products[{{$index}}][name]" value="{{$product['name'] ?? ''}}">
                                    <div>
                                        <ul class="list-group" id="suggestedProducts" style="max-height: 200px; overflow-y: auto;">

                                        </ul>
                                    </div>
                                    @error("products.$index.name")
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="align-middle">
                                    <select name="products[{{ $index }}][variant]" class="form-select variant-select">
                                        <option value="" disabled>Choose variant</option>

                                        @if(old("products.$index.variant"))

                                            @php
                                                $variantJson = old("products.$index.variant");
                                                $variant = $variantJson ? json_decode($variantJson, true) : null;
                                            @endphp

                                            @if($variant)
                                                <option value="{{ $variantJson }}" selected>{{ $variant['title'] }}</option>
                                            @else
                                                <option value="" disabled selected>Choose variant</option>
                                            @endif

                                        @endif
                                    </select>
                                    @error("products.$index.variant")
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </td>


                                <td class="align-middle">
                                    <input type="number" name="products[{{$index}}][price]" class="form-control variant-price" step="0.01" value="{{$product['price'] ?? ''}}">
                                    @error("products.$index.price")
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </td>
                                <td class="text-center align-middle">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-variant" >Remove</button>
                                </td>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <div>
                        <input type="hidden" id="wholesalerProducts" name="wholesalerProducts" value="">
                    </div>
                </div>
{{--                products--}}

                <div class="d-flex gap-2 mt-5">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <button type="reset" class="btn btn-secondary">Reset</button>
                </div>
            </div>
        </form>

        <form id="creditForm" class="form" method="post" action="{{route('users.add-credits', $user)}}">

            @csrf

            <div class="card p-5 shadow-sm">
                <h4 class="header" id="credit">Current Credit: ${{isset($user->credits) ? $user->credits : '0.00'}}</h4>
                <div class="row mb-3 mt-3">
                    <div class="col-md-6">
                        <label for="credit" class="form-label">Credit Amount</label>
                        <input class="form-control" type="number" name="credit" id="credit" placeholder="Enter credit amount" step="0.01" value="{{old('credit')}}">
                        @error('credit')
                        <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="reason" class="form-label">Choose a Reason</label>
                        <select class="form-select" name="reason" id="reason">
                            <option value="missing_product">Missing Product</option>
                            <option value="no_stock">No Stock</option>
                            <option value="product_quality">Product Quality</option>
                            <option value="delivery_issue">Delivery Issue</option>
                            <option value="foreign_object">Foreign Object</option>
                            <option value="technical_error">Technical Error</option>
                            <option value="duplicate_order">Duplicate Order</option>
                        </select>
                    </div>
                </div>

                <div class="mt-3">
                    @if (session('credit'))
                        <div class="alert alert-success">
                            {{ session('credit') }}
                        </div>
                    @endif
                    <button type="submit" id="add" class="btn btn-primary px-3 py-1">ADD CREDIT</button>
                </div>


            </div>
        </form>
        @unless($user->logs->isEmpty())
        <div class="container border rounded mt-5 bg-white p-5 shadow-sm">
            <h4>Credit Logs</h4>
            <table class="table table-hover w-100">
                <thead class="">
                    <tr>
                        <th class="table-header">Date</th>
                        <th>Credit Amount</th>
                        <th>Previous Balance</th>
                        <th>New Balance</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($user->logs->reverse() as $log)
                    <tr>
                        <td>
                            {{$log->created_at}}
                        </td>
                        <td>
                            {{$log->credit_amount}}
                        </td>
                        <td>
                            {{$log->previous_balance}}
                        </td>
                        <td>
                            {{$log->new_balance}}
                        </td>
                        <td>
                            {{$log->description}}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @endunless
    </div>

    @vite(['resources/js/user.js'])

@endsection
