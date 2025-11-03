<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateOrderRequest extends FormRequest
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
            'first_name' => ['required', 'string', 'max:50'],
            'last_name'  => ['required', 'string', 'max:50'],
            'street_address' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:50'],
            'country' => ['required', 'in:india'],
            'state' => ['required', 'in:surat,mumbai,baroda,pune'],
            'postcode' => ['required', 'digits:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'Enter First Name.',
            'last_name.required' => 'Enter Last Name.',
            'street_address.required' => 'Enter Street Address.',
            'city.required' => 'Enter City.',
            'country.required' => 'Please Select a Country.',
            'state.required' => 'Please Select a State.',
            'postcode.required' => 'Please Enter a postcode.',
            'postcode.digits' => 'Postcode should be of 6 digits.',
        ];
    }
}
