@extends('layout.app')

@section('title', 'Product Index')

@section('content')
    <div class="container-fluid mt-5">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <div class="section-header d-flex"><h2 class="mb-4 header">Products</h2>

            <div class="ms-auto">
                <a href="{{route('products.create')}}" class="btn btn-warning colored-text">Add Product</a>
            </div>
        </div>
        <table class="table table-bordered table-striped table-hover w-100 mb-0">
            <thead class="table-dark">
            <tr>
                <th class="table-header">ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($products as $product)

                <tr>
                    <td>{{$product->id}}</td>
                    <td>{{$product->title}}</td>
                    <td>{{$product->description}}</td>
                    <td>{{ $product->status == 1 ? 'Active' : 'Inactive' }}
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <form action="{{ route('products.edit', $product) }}" method="POST">
                                @csrf
                                @method('get')
                                <button type="submit" class="btn btn-primary">Edit</button>
                            </form>
                            <form action="{{ route('products.destroy', $product) }}" method="POST">
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
    {{ $products->links() }}

@endsection
