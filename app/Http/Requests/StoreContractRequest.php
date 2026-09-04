<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['nullable', 'exists:customers,id'],
            'quote_id' => ['nullable', 'exists:quotes,id'],
            'number' => ['nullable', 'string', 'max:100', 'unique:contracts,number'],
            'content' => ['nullable', 'string'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'status' => ['nullable', 'in:draft,sent,signed,expired,cancelled'],
        ];
    }
}
