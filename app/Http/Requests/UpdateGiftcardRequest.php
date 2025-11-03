<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGiftcardRequest extends FormRequest
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
            'code'  => ['required', 'digits_between:8,12'],
            'initial_balance'  => ['required'],
            'balance' => ['required', 'numeric', 'decimal:0,2', 'max:99999999.99', 'lte:initial_balance'],
            'expiry_date' => ['required', 'date', 'after:today'],
            'status' => ['required', 'boolean']
        ];
    }
    public function messages(): array
    {
        return [
            'code.required' => 'Enter Code.',
            'code.digits_between' => 'Code should be between 8 to 12 digits.',
            'balance.required' => 'Enter Balance you want to gift.',
            'balance.decimal' => 'Balance can contain maximum of 2 decimals.',
            'balance.max' => 'Balance can be maximum 8 digits long.',
            'balance.lte' => 'Balance should be less than or equal to initial balance.',
            'expiry_date.required' => 'Please enter an expiry date.',
            'expiry_date.after' => 'Expiry date should be after today.',
            'status.required' => 'Status is required',
        ];
    }
}
