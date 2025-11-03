@extends('layout.app')

@section('title', 'Edit Page')

@section('content')
    <div class="container form-section mt-5">
        <h2 class="mb-4">Edit Product - {{$product->id}}</h2>

        <form id="productEditForm" action="{{route('products.update', $product)}}" method="POST" novalidate enctype="multipart/form-data">

            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" id="title" name="title" class="form-control" value="{{ $product->title }}" placeholder="Enter Title">
                    @error('title')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="description" class="form-label">Description</label>
                    <input type="text" id="description" name="description" class="form-control" value="{{ $product->description }}" placeholder="Enter Description">
                    @error('description')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="my-3">
                <label for="images" class="form-label">Images</label>
                <input type="file" id="images" name="images[]" class="form-control" multiple>
                @error('images')
                <div class="text-danger">{{ $message }}</div>
                @enderror

            </div>
            <div class="d-flex flex-wrap gap-3 align-items-start justify-content-start overflow-hidden">
                @if(!empty($product->image_urls))
                    @foreach($product->image_urls as $image)
                        <div class="col-md-3 mb-3">
                            <div class="card">
                                <img src="{{ $image }}" class="card-img-top rounded" style="object-fit: cover; height: 250px;" alt="Product image">
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted">No images available.</p>
                @endif

            </div>


            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-select">
                    <option value="" disabled {{ $product->status === null ? 'selected' : '' }}>Choose status</option>
                    <option value="1" {{ $product->status == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ $product->status == '0' ? 'selected' : '' }}>Inactive</option>
                </select>

                @error('status')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Available Sizes & Prices:</label>

                <table id="sizeTable" class="table table-bordered align-middle">
                    <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Price</th>
                        <th>SKU</th>
                        <th style="width: 120px;">Action</th>
                    </tr>
                    </thead>
                    <tbody id="tableBody">
                    @foreach($product->variants as $variant)
                        <tr>
                            <td>
                                <input type="text" class="form-control" name="variants[{{$loop->iteration - 1 }}][title]" placeholder="Enter Title" value="{{ $variant->title }}">
                                @error('variants.*.title')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </td>
                            <td>
                                <input type="number" class="form-control" name="variants[{{ $loop->iteration - 1 }}][price]" step="0.01" placeholder="0.00" value="{{ $variant->price }}">
                                @error('variants.*.price')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </td>
                            <td>
                                <input type="text" class="form-control" name="variants[{{$loop->iteration - 1 }}][sku]" placeholder="SKU code" value="{{ $variant->sku }}">
                                @error('variants.*.sku')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger remove-size" >Remove</button>
                            </td>
                            @error('variants')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </tr>
                    @endforeach

                    </tbody>
                </table>

                <div class="text-end">
                    <button type="button" id="addRow" class="btn btn-sm btn-primary">+ Add Size</button>
                </div>
            </div>

            <div class="mb-3 w-50">
                <label for="tags" class="form-label">Tags</label>
                <input type="text" id="tags" name="tags" class="form-control" placeholder="Enter Tag" autocomplete="off">

                <div class="mb-3">
                    <div class="mb-3">
                        <ul class="list-group" id="suggestedIds" style="max-height: 200px; overflow-y: auto;">

                        </ul>
                    </div>
                </div>
            </div>
            <div class="mt-0 w-50">
                <div class="selectedIds">

                </div>
            </div>
            <div class="hidden-input">
                <input type="hidden" id="productTags" name="productTags" value="{{ $product->tags->pluck('id') }}">
            </div>
            @error('productTags')
            <div class="text-danger">{{ $message }}</div>
            @enderror
            <div>
                @foreach($product->tags as $tag)
                    <span class="tag-tablet ms-3 bg-primary text-white rounded-pill px-3 py-1 d-inline-flex align-items-center mt-3" data-id="{{$tag->id}}">
                        {{$tag->name}}
                        <button type="button" class="tag-remove-btn btn-close btn-close-white ms-2" aria-label="Remove" style="font-size: 0.6rem;"></button>
                    </span>
                @endforeach
            </div>


            <div class="d-flex gap-2 mt-5">
                <button type="submit" class="btn btn-primary">Submit</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
        </form>
    </div>
    @vite(['resources/js/product.js'])
@endsection
