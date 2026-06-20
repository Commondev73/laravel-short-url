<?php

namespace App\Repositories\Eloquent;

use App\Models\RefreshToken;
use App\Repositories\Interfaces\RefreshTokenRepositoryInterface;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    public function create(array $data): RefreshToken
    {
        return RefreshToken::create($data);
    }

    public function findActiveByTokenHash(string $tokenHash): ?RefreshToken
    {
        return RefreshToken::query()
            ->where('token_hash', $tokenHash)
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now())
            ->first();    
    }

    public function revoke(int $id): void
    {
        RefreshToken::query()
            ->where('id', $id)
            ->whereNull('revoked_at')
            ->update(['revoked_at' => now()]);
    }
}
