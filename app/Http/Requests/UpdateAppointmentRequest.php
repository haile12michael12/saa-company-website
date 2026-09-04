<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['nullable', 'exists:customers,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'starts_at' => ['sometimes', 'required', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'status' => ['sometimes', 'required', 'in:pending,confirmed,completed,cancelled'],
            'location' => ['nullable', 'string', 'max:255'],
        ];
    }
}
