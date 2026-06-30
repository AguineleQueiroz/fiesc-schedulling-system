<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'day_of_week' => ['required', 'integer', Rule::in(range(0, 6))],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'end_time.after' => 'A hora final deve ser maior que a hora inicial.',
        ];
    }
}
