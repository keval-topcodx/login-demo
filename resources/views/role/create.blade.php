@extends('layout.app')

@section('title', 'Create Page')

@section('content')
    <div class="container mt-5 d-flex justify-content-center">
        <div class="card shadow-sm w-75">
            <div class="card-body p-4">
                <h2 class="mb-4 text-center">Create Role</h2>

                <form id="rolesForm" action="{{ route('roles.store') }}" method="POST" novalidate>
                @csrf

                <!-- User Name Field -->
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" placeholder="Enter Name">
                        @error('name')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Permissions Section -->
                    <div class="mb-4">
                        <label class="form-label d-block mb-2">Assign Permissions</label>

                        <div class="border rounded p-3 bg-light">
                            @foreach($permissions as $permission)
                                <div class="form-check mb-2">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="permissions[]"
                                        value="{{ $permission->name }}"
                                        id="{{ $permission->name }}"
                                        {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label text-capitalize" for="{{ $permission->name }}">
                                        {{ str_replace('_', ' ', $permission->name) }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        @error('permissions')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Form Buttons -->
                    <div class="d-flex justify-content-end gap-2">
                        <button type="reset" class="btn btn-outline-secondary">Reset</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>

                </form>
            </div>
        </div>
    </div>


@endsection
