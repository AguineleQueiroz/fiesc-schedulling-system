<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserService
{
    public function all(): Collection
    {
        return User::orderBy('name')->get();
    }

    public function attendants(): Collection
    {
        return User::where('role', UserRole::Attendant)->orderBy('name')->get();
    }

    public function findOrFail(int $id): User
    {
        return User::findOrFail($id);
    }

    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $target, array $data, User $editor): User
    {
        // Edição de si mesmo nunca altera o próprio perfil (gap #2 do planejamento)
        if ($editor->id === $target->id) {
            unset($data['role']);
        }

        $target->update($data);

        return $target->fresh();
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
