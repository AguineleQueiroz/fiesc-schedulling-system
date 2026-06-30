<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function create(array $data): User
    {
        return User::create($data);
    }

    public function update(User $target, array $data, User $editor): User
    {
        if ($editor->id === $target->id) {
            unset($data['role']);
        }

        $target->update($data);

        return $target->fresh();
    }
}
