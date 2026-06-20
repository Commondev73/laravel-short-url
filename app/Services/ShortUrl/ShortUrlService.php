<?php

namespace App\Services\ShortUrl;

use App\Models\ShortUrl;
use App\Repositories\Interfaces\ShortUrlRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use UnauthorizedException;

class ShortUrlService
{
    private const SHORT_CODE_LENGTH = 7;

    private const MAX_GENERATION_COUNT = 5;

    private const DEFAULT_EXPIRATION_DAYS = 30;

    public function __construct(
        private ShortUrlRepositoryInterface $shortUrlRepository,
        private ShortUrlCacheService $cache
    ) {}

    public function create(int $userId, array $input): ShortUrl
    {
        $data = [
            'user_id' => $userId,
            'original_url' => $input['original_url'],
            'short_code' => $this->generateUniqueShortCode(),
            'title' => $input['title'] ?? null,
            'is_active' => $input['is_active'] ?? true,
            'expires_at' => $input['expires_at'] ?? now()->addDays(self::DEFAULT_EXPIRATION_DAYS),
        ];

        return $this->shortUrlRepository->create($data);
    }

    public function findById(int $id): ShortUrl
    {
        $shortUrl = $this->cache->rememberById(
            $id,
            fn () => $this->shortUrlRepository->findById($id)?->toArray()
        );

        if ($shortUrl === null) {
            throw new NotFoundHttpException('Short URL not found.');
        }

        return $shortUrl;
    }

    public function findByIdAndUserId(int $id, int $userId): ShortUrl
    {
        $shortUrl = $this->findById($id);

        if ($shortUrl->user_id !== $userId) {
            throw new UnauthorizedException('You are not authorized to view this short URL.');
        }

        return $shortUrl;
    }

    public function findByShortCode(string $shortCode): ShortUrl
    {
        $shortUrl = $this->cache->rememberByShortCode(
            $shortCode,
            fn () => $this->shortUrlRepository->findByShortCode($shortCode)?->toArray()
        );

        if ($shortUrl === null || ! $shortUrl->isAccessible()) {
            throw new NotFoundHttpException('Short URL not found or inaccessible.');
        }

        return $shortUrl;
    }

    public function paginate(int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $perPage = max(1, min($perPage, 100));
        $page = max(1, $page);

        return $this->shortUrlRepository->paginate($perPage, $page);
    }

    public function paginateByUserId(int $userId, int $perPage = 15, int $page = 1): LengthAwarePaginator
    {
        $perPage = max(1, min($perPage, 100));
        $page = max(1, $page);

        return $this->shortUrlRepository->paginateByUserId($userId, $perPage, $page);
    }

    public function updateById(int $id, array $input): ShortUrl
    {
        $this->findById($id);

        $updated = $this->shortUrlRepository->update($id, $input);

        $this->cache->forget($updated);

        return $updated;
    }

    public function updateByIdAndUserId(int $id, int $userId, array $input): ShortUrl
    {
        $shortUrl = $this->findById($id);

        if ($shortUrl->user_id !== $userId) {
            throw new UnauthorizedException('You are not authorized to update this short URL.');
        }

        $updated = $this->shortUrlRepository->update($id, $input);

        $this->cache->forget($updated);

        return $updated;
    }

    public function clickCount(int $id): void
    {
        $shortUrl = $this->shortUrlRepository->incrementClickCount($id);

        $this->cache->set($shortUrl);
    }

    public function deleteById(int $id): void
    {
        $shortUrl = $this->findById($id);

        $this->cache->forget($shortUrl);

        $this->shortUrlRepository->delete($id);
    }

    public function deleteByIdAndUserId(int $id, int $userId): void
    {
        $shortUrl = $this->findById($id);

        if ($shortUrl->user_id !== $userId) {
            throw new UnauthorizedException('You are not authorized to delete this short URL.');
        }

        $this->cache->forget($shortUrl);

        $this->shortUrlRepository->delete($id);
    }
    
    private function generateUniqueShortCode(): string
    {
        for ($count = 0; $count < self::MAX_GENERATION_COUNT; $count++) {
            $shortCode = Str::lower(Str::random(self::SHORT_CODE_LENGTH));

            if ($this->shortUrlRepository->findByShortCode($shortCode) === null) {
                return $shortCode;
            }
        }

        throw new RuntimeException('Unable to generate a unique short code.');
    }
}
