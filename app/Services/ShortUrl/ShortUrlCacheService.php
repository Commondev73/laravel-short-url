<?php

namespace App\Services\ShortUrl;

use App\Models\ShortUrl;
use App\Repositories\Cache\CacheRepositoryInterface;
use Closure;

class ShortUrlCacheService
{
    private const TTL_SECONDS = 3600;

    private const KEY_PREFIX_CODE = 'short_url:code:';

    private const KEY_PREFIX_ID = 'short_url:id:';

    public function __construct(
        private CacheRepositoryInterface $cache
    ) {}

    public function rememberByShortCode(string $shortCode, Closure $callback): ?ShortUrl
    {
        $TTL = 86400; // 24 hours
        return $this->cache->remember(
            self::KEY_PREFIX_CODE . $shortCode,
            $TTL,
            $callback
        );
    }

    public function rememberById(int $id, Closure $callback): ?ShortUrl
    {
        return $this->cache->remember(
            self::KEY_PREFIX_ID . $id,
            self::TTL_SECONDS,
            $callback
        );
    }

    public function forget(ShortUrl $shortUrl): void
    {
        $this->cache->forget(self::KEY_PREFIX_CODE . $shortUrl->short_code);
        $this->cache->forget(self::KEY_PREFIX_ID . $shortUrl->id);
    }
}
