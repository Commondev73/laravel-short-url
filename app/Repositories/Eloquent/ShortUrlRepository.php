<?php

namespace App\Repositories\Eloquent;

use App\Models\ShortUrl;
use App\Repositories\Interfaces\ShortUrlRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ShortUrlRepository implements ShortUrlRepositoryInterface
{
    public function create(array $data): ShortUrl
    {
        return ShortUrl::create($data);
    }

    public function findById(int $id): ?ShortUrl
    {
        return ShortUrl::query()
            ->where('id', $id)
            ->first();
    }

    public function findByShortCode(string $shortCode): ?ShortUrl
    {
        return ShortUrl::query()
            ->where('short_code', $shortCode)
            ->first();
    }

    public function paginateByUserId(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return ShortUrl::query()
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }

    public function incrementClickCount(int $id): void
    {
        ShortUrl::query()
            ->where('id', $id)
            ->increment('click_count');
    }

    public function update(int $id, array $data): bool
    {
        return (bool) ShortUrl::query()
            ->where('id', $id)
            ->update($data);
    }
}
