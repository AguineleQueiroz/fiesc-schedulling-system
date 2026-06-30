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
            'client_name'  => ['required', 'string', 'max:255'],
            'client_phone' => ['required', 'string', 'max:20'],
            'date'         => ['required', 'date', 'after_or_equal:today'],
            'start_time'   => ['required', 'date_format:H:i'],
            'end_time'     => ['required', 'date_format:H:i', 'after:start_time'],
        ];
    }

    public function messages(): array
    {
        return [
            'attendant_id.required'  => 'O atendente é obrigatório.',
            'attendant_id.integer'   => 'Atendente inválido.',
            'attendant_id.exists'    => 'O atendente selecionado não existe.',
            'client_name.required'   => 'O nome do cliente é obrigatório.',
            'client_name.max'        => 'O nome do cliente não pode ter mais de 255 caracteres.',
            'client_phone.required'  => 'O telefone do cliente é obrigatório.',
            'client_phone.max'       => 'O telefone não pode ter mais de 20 caracteres.',
            'date.required'          => 'A data é obrigatória.',
            'date.date'              => 'Informe uma data válida.',
            'date.after_or_equal'    => 'Não é possível agendar para datas passadas.',
            'start_time.required'    => 'O horário de início é obrigatório.',
            'start_time.date_format' => 'O horário de início deve estar no formato HH:MM.',
            'end_time.required'      => 'O horário de término é obrigatório.',
            'end_time.date_format'   => 'O horário de término deve estar no formato HH:MM.',
            'end_time.after'         => 'A hora final deve ser maior que a hora inicial.',
        ];
    }
}
