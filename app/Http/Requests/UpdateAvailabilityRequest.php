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
            'day_of_week.required'   => 'O dia da semana é obrigatório.',
            'day_of_week.integer'    => 'O dia da semana é inválido.',
            'day_of_week.in'         => 'O dia da semana deve ser entre 0 (domingo) e 6 (sábado).',
            'start_time.required'    => 'A hora inicial é obrigatória.',
            'start_time.date_format' => 'A hora inicial deve estar no formato HH:MM.',
            'end_time.required'      => 'A hora final é obrigatória.',
            'end_time.date_format'   => 'A hora final deve estar no formato HH:MM.',
            'end_time.after'         => 'A hora final deve ser maior que a hora inicial.',
            'active.boolean'         => 'O campo ativo deve ser verdadeiro ou falso.',
        ];
    }
}
