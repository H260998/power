<?php

namespace App\Http\Requests\Storefront;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['bail', 'required', 'string', 'regex:/\A[234579][0-9]{7}\z/'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required' => __('storefront.phone_tunisia_invalid'),
            'phone.string' => __('storefront.phone_tunisia_invalid'),
            'phone.regex' => __('storefront.phone_tunisia_invalid'),
        ];
    }
}
