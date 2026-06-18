<?php

namespace App\Repositories\Interfaces;

use App\Models\RefreshToken;

interface RefreshTokenRepositoryInterface
{
    public function create(array $data): RefreshToken;

    public function findActiveByTokenHash(string $tokenHash): ?RefreshToken;

    public function revoke(int $id): void;
}
