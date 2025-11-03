@extends('layout.app')

@section('title', 'Index Page')

@section('content')
    <div class="container-fluid mt-5">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <div class="section-header d-flex"><h2 class="mb-4 header">Users Table</h2>

            <div class="ms-auto">
                <a href="{{route('users.create')}}" class="btn btn-warning colored-text">Add User</a>
            </div>
        </div>
        <table class="table table-bordered table-striped table-hover w-100">
            <thead class="table-dark">
            <tr>
                <th class="table-header">ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Gender</th>
                <th>Phone Number</th>
                <th>Hobbies</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)

                <tr>
                    <td>{{$user->id}}</td>
                    <td>{{$user->first_name}}</td>
                    <td>{{$user->last_name}}</td>
                    <td>{{$user->email}}</td>
                    <td>{{$user->gender}}</td>
                    <td>{{$user->phone_no}}</td>
                    <td>{{$user->hobbies}}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <form action="{{ route('users.edit', $user) }}" method="POST">
                                @csrf
                                @method('get')
                                <button type="submit" class="btn btn-primary">Edit</button>
                            </form>
                            <form action="{{ route('users.destroy', $user) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </td>

                </tr>
            @endforeach

            </tbody>

        </table>

    </div>
    {{ $users->links() }}

@endsection
