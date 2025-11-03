@extends('layout.app')

@section('title', 'Create Page')

@section('content')
    <div class="container form-section mt-5">
        <h2 class="mb-4">Create Product</h2>

        <form id="productForm" action="{{route('products.store')}}" method="POST" novalidate enctype="multipart/form-data">

            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" id="title" name="title" class="form-control" value="{{ old('title') }}" placeholder="Enter Title">
                    @error('title')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="description" class="form-label">Description</label>
                    <input type="text" id="description" name="description" class="form-control" value="{{ old('description') }}" placeholder="Enter Description">
                    @error('description')
                    <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="my-3">
                <label for="images" class="form-label">Images</label>
                <input type="file" id="images" name="images[]" class="form-control"  value="{{ old('images') }}" multiple>
                @error('images')
                <div class="text-danger">{{ $message }}</div>
                @enderror

            </div>


            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select id="status" name="status" class="form-select">
                    <option value="" disabled {{ old('status') === null ? 'selected' : '' }}>Choose status</option>
                    <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
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
                    <tr>
                        <td>

                            <input type="text" class="form-control" name="variants[0][title]" placeholder="Enter Title" value="{{ old('variants.0.title') }}">
                            @error('variants.*.title')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </td>
                        <td>
                            <input type="number" class="form-control" name="variants[0][price]" step="0.01" placeholder="0.00" value="{{old('variants.0.price')}}">
                            @error('variants.*.price')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </td>
                        <td>
                            <input type="text" class="form-control" name="variants[0][sku]" placeholder="SKU code" value="{{old('variants.0.sku')}}">
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

{{--                    <select id="mySelect" class="form-select" size="4" style="display: none">--}}

{{--                    </select>--}}
                </div>
            </div>
            <div class="mt-0 w-50">
                <div class="selectedIds">

                </div>
            </div>
            <div>
                <input type="hidden" id="productTags" name="productTags" value="">
            </div>
            @error('productTags')
            <div class="text-danger">{{ $message }}</div>
            @enderror



            <div class="d-flex gap-2 mt-5">
                <button type="submit" class="btn btn-primary">Submit</button>
                <button type="reset" class="btn btn-secondary">Reset</button>
            </div>
        </form>
    </div>
    @vite(['resources/js/product.js'])
@endsection
