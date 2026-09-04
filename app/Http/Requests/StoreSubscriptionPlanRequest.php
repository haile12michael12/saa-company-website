<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubscriptionPlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'billing_interval' => ['required', 'in:monthly,yearly,quarterly'],
            'features' => ['nullable', 'array'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
