<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::enum(UserRole::class)],
            'password' => ['required', 'string', Password::min(8), 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'                  => 'O nome é obrigatório.',
            'name.max'                       => 'O nome não pode ter mais de 255 caracteres.',
            'email.required'                 => 'O e-mail é obrigatório.',
            'email.email'                    => 'Informe um e-mail válido.',
            'email.max'                      => 'O e-mail não pode ter mais de 255 caracteres.',
            'email.unique'                   => 'Este e-mail já está em uso.',
            'role.required'                  => 'O tipo de usuário é obrigatório.',
            'role.enum'                      => 'O tipo de usuário selecionado é inválido.',
            'password.required'              => 'A senha é obrigatória.',
            'password.min'                   => 'A senha deve ter no mínimo 8 caracteres.',
            'password.confirmed'             => 'A confirmação de senha não confere.',
            'password_confirmation.required' => 'A confirmação de senha é obrigatória.',
        ];
    }
}
