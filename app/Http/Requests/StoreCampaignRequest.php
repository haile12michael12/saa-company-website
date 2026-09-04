<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'subject' => ['required', 'string', 'max:255'],
            'type' => ['nullable', 'in:email,newsletter,sms'],
            'content' => ['required', 'string'],
            'target_audience' => ['nullable', 'array'],
            'scheduled_at' => ['nullable', 'date'],
        ];
    }
}
