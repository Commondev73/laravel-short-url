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
            fn () => $this->shortUrlRepository->findById($id)
        );

        if ($shortUrl === null) {
            throw new NotFoundHttpException('Short URL not found.');
        }

        return $shortUrl;
    }

    public function findByShortCode(string $shortCode): ShortUrl
    {
        $shortUrl = $this->cache->rememberByShortCode(
            $shortCode,
            fn () => $this->shortUrlRepository->findByShortCode($shortCode)
        );

        if ($shortUrl === null || ! $shortUrl->isAccessible()) {
            throw new NotFoundHttpException('Short URL not found or inaccessible.');
        }

        return $shortUrl;
    }

    public function paginateByUserId(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        $perPage = max(1, min($perPage, 100));

        return $this->shortUrlRepository->paginateByUserId($userId, $perPage);
    }

    public function update(int $id, int $userId, array $input): ShortUrl
    {
        $shortUrl = $this->shortUrlRepository->findById($id);

        if ($shortUrl === null) {
            throw new NotFoundHttpException('Short URL not found.');
        }

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
