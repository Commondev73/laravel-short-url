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

    public function paginateByUserId(int $userId, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $columns = ['*'];
        $pageName = 'page';
        
        return ShortUrl::query()
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage, $columns, $pageName, $page);
    }

    public function paginate(int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $columns = ['*'];
        $pageName = 'page';

        return ShortUrl::query()
            ->with('user')
            ->latest()
            ->paginate($perPage, $columns, $pageName, $page);
    }

    public function incrementClickCount(int $id): ShortUrl
    {
        $shortUrl = ShortUrl::findOrFail($id);
        
        $shortUrl->increment('click_count');

        return $shortUrl;
    }

    public function update(int $id, array $data): ShortUrl
    {
        $shortUrl = ShortUrl::findOrFail($id);

        $shortUrl->update($data);

        return $shortUrl;
    }

    public function delete(int $id): void
    {
        ShortUrl::findOrFail($id)->delete();
    }
}
