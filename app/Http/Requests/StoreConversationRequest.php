<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['nullable', 'exists:customers,id'],
            'channel' => ['nullable', 'string', 'in:email,chat,sms,whatsapp'],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['nullable', 'string'],
        ];
    }
}
