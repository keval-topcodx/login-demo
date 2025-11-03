<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
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
        $userId = $this->route('user')->id;
        return [
            'first_name' => ['bail','required', 'string', 'max:50'],
            'last_name'  => ['bail','required', 'string', 'max:50'],
            'email'      => [
                'bail',
                'required',
                'email',
                Rule::unique('users')->ignore($userId),
            ],
            'password'   => [
                'bail',
                'sometimes',
                'nullable',
                'confirmed',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers(),
            ],
            'phone_no' => ['required', 'regex:/^\+?[0-9\s\-\(\)]{7,20}$/'],
            'gender'     => ['required', 'in:male,female'],
            'hobbies'   => ['required', 'array', 'min:1'],
            'hobbies.*' => ['in:reading,sports,music,travel,coding'],
            'roles' => ['nullable', 'array'],
        ];
    }
}
