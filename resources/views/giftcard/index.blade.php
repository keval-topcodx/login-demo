@extends('layout.app')

@section('title', 'Giftcards Index')

@section('content')
    <div class="container-fluid pt-5">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <div class="section-header d-flex"><h2 class="mb-4 header">Giftcards</h2>

            <div class="ms-auto">
                <a href="{{route('giftcards.create')}}" class="btn btn-warning colored-text">Add Giftcard</a>
            </div>
        </div>
        <table class="table table-bordered table-striped table-hover w-100 mb-0">
            <thead class="table-dark">
            <tr>
                <th class="table-header">ID</th>
                <th>Code</th>
                <th>Initial Balance</th>
                <th>Balance</th>
                <th>User id</th>
                <th>Expiry date</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @foreach($giftcards as $giftcard)

                <tr>
                    <td>{{$giftcard->id}}</td>
                    <td>{{$giftcard->code}}</td>
                    <td>{{$giftcard->initial_balance}}</td>
                    <td>{{$giftcard->balance}}</td>
                    <td>{{$giftcard->user_id}}</td>
                    <td>{{$giftcard->expiry_date}}</td>
                    <td>
                        {{ $giftcard->status == 1 ? 'Active' : 'Inactive' }}
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <form action="{{ route('giftcards.edit', $giftcard) }}" method="POST">
                                @csrf
                                @method('get')
                                <button type="submit" class="btn btn-primary">Edit</button>
                            </form>
                            <form action="{{ route('giftcards.destroy', $giftcard) }}" method="POST">
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
