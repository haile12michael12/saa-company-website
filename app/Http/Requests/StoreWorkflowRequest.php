<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkflowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'trigger' => ['required', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'conditions' => ['nullable', 'array'],
            'actions' => ['nullable', 'array'],
            'actions.*.type' => ['required', 'string'],
            'actions.*.configuration' => ['nullable', 'array'],
        ];
    }
}
