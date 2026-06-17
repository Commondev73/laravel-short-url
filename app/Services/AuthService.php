<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private UserService $userService,
        private RefreshTokenService $refreshTokenService
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

    public function login(array $input): array
    {
        $user = $this->userService->findForLogin($input);

        if ($user === null || ! Hash::check($input['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $accessToken = auth('api')->login($user);

        return [
            'user' => $user,
            'access_token' => $accessToken,
            'refresh_token' => $this->refreshTokenService->issue($user),
            'token_type' => 'Bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ];
    }
}
