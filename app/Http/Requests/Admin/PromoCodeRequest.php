<?php

namespace App\Http\Requests\Admin;

use App\Enums\PromoType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class PromoCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $promoCode = $this->route('promo_code');

        return [
            'code' => ['required', 'string', 'max:50', 'alpha_dash', Rule::unique('promo_codes', 'code')->ignore($promoCode?->id)],
            'type' => ['required', new Enum(PromoType::class)],
            'value' => [
                'required', 'numeric', 'min:0',
                function ($attribute, $value, $fail) {
                    if ($this->input('type') === PromoType::Percentage->value && $value > 100) {
                        $fail(__('admin.promo_percentage_max'));
                    }
                },
            ],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'max_uses' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
