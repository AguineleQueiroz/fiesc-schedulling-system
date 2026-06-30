<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'attendant_id' => ['required', 'integer', 'exists:users,id'],
            'client_name' => ['required', 'string', 'max:255'],
            'client_phone' => ['required', 'string', 'max:20'],
            'date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
        ];
    }

    public function messages(): array
    {
        return [
            'date.after_or_equal' => 'Não é possível agendar para datas passadas.',
            'end_time.after' => 'A hora final deve ser maior que a hora inicial.',
        ];
    }
}
