@extends('layout.app')

@section('title', 'User Orders Index')

@section('content')
    <div class="container-fluid pt-5">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <div class="section-header d-flex"><h2 class="mb-4 header">Orders</h2>

        </div>
        <table class="table table-bordered table-striped table-hover w-100 mb-0">
            <thead class="table-dark">
            <tr>
                <th class="table-header">ID</th>
                <th>User Name</th>
                <th>Subtotal</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($orders as $order)

                <tr>
                    <td>{{$order->id}}</td>
                    <td>{{ $order->user->first_name }} {{$order->user->last_name}}</td>
                    <td>{{$order->subtotal}}</td>
                    <td>{{$order->created_at}}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <form action="{{ route('order.edit', $order) }}" method="POST">
                                @csrf
                                @method('get')
                                <button type="submit" class="btn btn-primary">Edit</button>
                            </form>
                            <form action="{{ route('order.destroy', $order) }}" method="POST">
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

@endsection
