<?php

namespace App\Services;

use App\Models\User;

class AuthService
{
    public function __construct(
        private UserService $userService
    ) {}

    public function register(array $input): User
    {
        $data = [
            'name' => $input['name'],
            'email' => $input['email'],
            'username' => $input['username'] ?? null,
            'password' => $input['password'],
            'role' => User::ROLE_USER,
        ];

        return $this->userService->create($data);
    }
}
