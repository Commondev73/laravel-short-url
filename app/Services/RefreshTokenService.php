<?php

namespace App\Services;

use App\Models\RefreshToken;
use App\Models\User;
use App\Repositories\Interfaces\RefreshTokenRepositoryInterface;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Carbon;

class RefreshTokenService
{
    private const TTL_DAYS = 30;

    public function __construct(
        private RefreshTokenRepositoryInterface $refreshTokenRepository
    ) {}

    public function issue(User $user): string
    {
        $plainToken = $this->generatePlainToken();
        $expiresAt = now()->addDays(self::TTL_DAYS);

        $this->refreshTokenRepository->create([
            'user_id' => $user->id,
            'token_hash' => $this->hashToken($plainToken),
            'expires_at' => $expiresAt,
        ]);

        return $plainToken;
    }

    public function findActive(string $plainToken): RefreshToken
    {
        $tokenHash = $this->hashToken($plainToken);

        $token = $this->refreshTokenRepository->findActiveByTokenHash($tokenHash);

        if ($token === null) {
            throw new AuthenticationException('The refresh token is invalid or expired.');
        }

        return $token;
    }

    public function revoke(int $id): void
    {
        $this->refreshTokenRepository->revoke($id);
    }

    public function rotate(string $plainToken): array
    {
        $refreshToken = $this->findActive($plainToken);

        $user = $refreshToken->user;
        $this->revoke($refreshToken->id);

        return [
            'plain_token' => $this->issue($user),
            'user' => $user,
        ];
    }

    private function generatePlainToken(): string
    {
        return bin2hex(random_bytes(64));
    }

    private function hashToken(string $plainToken): string
    {
        return hash_hmac('sha256', $plainToken, config('auth.refresh_token_hash_key'));
    }
}
