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

        $refreshToken = $this->refreshTokenService->issue($user);

        return $this->createAuthTokens($user, $refreshToken);
    }

    public function refreshToken(string $refreshToken): array
    {
        $result = $this->refreshTokenService->rotate($refreshToken);

        if ($result === null) {
            throw ValidationException::withMessages([
                'refresh_token' => ['The refresh token is invalid or expired.'],
            ]);
        }

        return $this->createAuthTokens($result['user'], $result['plain_token']);
    }

    private function createAuthTokens(User $user, string $refreshToken): array
    {
        $accessToken = auth('api')->login($user);
        $expiresIn = auth('api')->factory()->getTTL() * 60;

        return [
            'user' => $user,
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer',
            'expires_in' => $expiresIn,
        ];
    }
}
