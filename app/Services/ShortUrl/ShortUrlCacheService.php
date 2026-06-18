<?php

namespace App\Services\ShortUrl;

use App\Models\ShortUrl;
use App\Repositories\Cache\CacheRepositoryInterface;
use Closure;

class ShortUrlCacheService
{
    private const TTL_SECONDS = 86400;

    private const KEY_PREFIX_CODE = 'short_url:code:';

    private const KEY_PREFIX_ID = 'short_url:id:';

    public function __construct(
        private CacheRepositoryInterface $cache
    ) {}

    public function rememberByShortCode(string $shortCode, Closure $callback): ?ShortUrl
    {
        $data = $this->cache->remember(
            self::KEY_PREFIX_CODE . $shortCode,
            self::TTL_SECONDS,
            $callback
        );

        return $this->hydrate($data);
    }

    public function rememberById(int $id, Closure $callback): ?ShortUrl
    {
        $data = $this->cache->remember(
            self::KEY_PREFIX_ID . $id,
            self::TTL_SECONDS,
            $callback
        );

        return $this->hydrate($data);
    }

    public function set(ShortUrl $shortUrl): void
    {
        $data = $shortUrl->toArray();
        $this->cache->set(self::KEY_PREFIX_CODE . $shortUrl->short_code, $data, self::TTL_SECONDS);
        $this->cache->set(self::KEY_PREFIX_ID . $shortUrl->id, $data, self::TTL_SECONDS);
    }
    
    public function forget(ShortUrl $shortUrl): void
    {
        $this->cache->forget(self::KEY_PREFIX_CODE . $shortUrl->short_code);
        $this->cache->forget(self::KEY_PREFIX_ID . $shortUrl->id);
    }

    private function hydrate(?array $data): ?ShortUrl
    {
        if ($data === null) {
            return null;
        }

        return ShortUrl::hydrate([$data])->first();
    }
}
