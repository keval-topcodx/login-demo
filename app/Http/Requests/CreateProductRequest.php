<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class CreateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:50'],
            'description'  => ['required', 'string', 'max:50'],
            'images' => ['required', 'array'],
            'images.*' => ['file', 'mimes:jpg,jpeg,png', 'max:2048'],
            'status' => ['required', 'boolean'],
            'variants' => ['required', 'array', 'min:1'],
            'variants.*.title' => ['required', 'string', 'max:50'],
            'variants.*.price' => ['required', 'numeric', 'decimal:0,2', 'min:0'],
            'variants.*.sku' => ['required', 'string', 'max:50'],
            'productTags' => ['json', 'nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Enter Title.',
            'description.required' => 'Enter Description.',
            'images.required' => 'Upload Images.',
            'images.*.mimes' => 'Image file type should be jpg, jpeg or png.',
            'images.*.max' => 'Image should be smaller than 2048 kb.',
            'status.required' => 'Enter Status.',
            'variants.*.title.required' => 'Enter Title of variant.',
            'variants.*.price.required' => 'Enter Price of variant.',
            'variants.*.price.decimal' => 'Price should have maximum 2 decimal points.',
            'variants.*.price.min' => 'Variant price should be more than Zero.',
            'variants.*.sku.required' => 'Enter sku of variant.',
            'variants.required' => 'Enter at least one variant',
        ];
    }
}
