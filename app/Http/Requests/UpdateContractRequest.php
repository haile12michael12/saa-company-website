<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $contractId = $this->route('contract') ? ($this->route('contract')->id ?? $this->route('contract')) : null;

        return [
            'customer_id' => ['nullable', 'exists:customers,id'],
            'quote_id' => ['nullable', 'exists:quotes,id'],
            'number' => ['sometimes', 'required', 'string', 'max:100', 'unique:contracts,number,' . $contractId],
            'content' => ['nullable', 'string'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'status' => ['sometimes', 'required', 'in:draft,sent,signed,expired,cancelled'],
        ];
    }
}
