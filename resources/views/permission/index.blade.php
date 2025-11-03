@extends('layout.app')

@section('title', 'Index Page')

@section('content')
    <div class="container-fluid mt-5">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white d-flex align-items-center">
                <h4 class="mb-0">Permissions Table</h4>
                <a href="{{ route('permissions.create') }}" class="btn btn-warning ms-auto">Add Permission</a>
            </div>

            <div class="card-body p-0">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-light">
                    <tr>
                        <th style="width: 10%;">ID</th>
                        <th style="width: 60%;">Name</th>
                        <th style="width: 30%;">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($permissions as $permission)
                        <tr>
                            <td>{{ $permission->id }}</td>
                            <td class="text-capitalize">{{ str_replace('_', ' ', $permission->name) }}</td>
                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ route('permissions.edit', $permission) }}" class="btn btn-primary btn-sm">Edit</a>

                                    <form action="{{ route('permissions.destroy', $permission) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this permission?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach

                    @if ($permissions->isEmpty())
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">No permissions found.</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{ $permissions->links() }}

@endsection
