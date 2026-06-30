<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        $target = $this->route('user');
        return $this->user()->canEdit($target);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'role' => ['sometimes', 'string', Rule::enum(UserRole::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.max'      => 'O nome não pode ter mais de 255 caracteres.',
            'role.enum'     => 'O tipo de usuário selecionado é inválido.',
        ];
    }
}
