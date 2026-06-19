<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function create(array $data): User
    {
        return User::create($data);
    }

    public function findById(int $id): ?User
    {
        return User::query()
            ->where('id', $id)
            ->first();
    }

    public function findEmail(string $email): ?User
    {
        return User::query()
            ->where('email', $email)
            ->first();
    }

    public function findUsername(string $username): ?User
    {
        return User::query()
            ->where('username', $username)
            ->first();
    }
}
