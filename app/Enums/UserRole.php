<?php

namespace App\Enums;

enum UserRole: string
{
    case Admin = 'admin';
    case Attendant = 'atendente';

    public function label(): string
    {
        return match ($this) {
            self::Admin => 'Administrador',
            self::Attendant => 'Atendente',
        };
    }
}
