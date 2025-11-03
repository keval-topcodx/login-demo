@extends('layout.app')

@section('title', 'Create Page')

@section('content')

    <div class="container mt-5 d-flex justify-content-center">
        <div class="card shadow-sm w-50">
            <div class="card-body p-4">
                <h2 class="mb-4 text-center">Create Permission</h2>

                <form id="permissionsForm" action="{{ route('permissions.store') }}" method="POST" novalidate>
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Permission Name</label>
                        <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" placeholder="Enter permission name">
                        @error('name')
                        <div class="text-danger small">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="reset" class="btn btn-outline-secondary">Reset</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
