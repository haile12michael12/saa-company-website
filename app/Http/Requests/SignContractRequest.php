<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'signer_name' => ['required', 'string', 'max:255'],
            'signer_email' => ['required', 'email', 'max:255'],
            'signature' => ['required', 'string'],
            'agree_terms' => ['required', 'accepted'],
        ];
    }
}
