<?php

namespace App\Repositories\Interfaces;

use App\Models\ShortUrl;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ShortUrlRepositoryInterface
{
    public function create(array $data): ShortUrl;

    public function findById(int $id): ?ShortUrl;

    public function findByShortCode(string $shortCode): ?ShortUrl;

    public function paginateByUserId(int $userId, int $perPage = 15): LengthAwarePaginator;

    public function incrementClickCount(int $id): void;

    public function update(int $id, array $data): ?ShortUrl;
}
