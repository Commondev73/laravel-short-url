<?php

namespace App\Repositories\Cache;

use Closure;
use Illuminate\Contracts\Cache\Repository as IlluminateCacheRepository;

class CacheRepository implements CacheRepositoryInterface
{
    public function __construct(
        private IlluminateCacheRepository $cache
    ) {}

    public function get(string $key): mixed
    {
        return $this->cache->get($key);
    }

    public function set(string $key, mixed $value, int $ttlSeconds): void
    {
        $this->cache->put($key, $value, $ttlSeconds);
    }

    public function forget(string $key): void
    {
        $this->cache->forget($key);
    }

    public function remember(string $key, int $ttlSeconds, Closure $callback): mixed
    {
        return $this->cache->remember($key, $ttlSeconds, $callback);
    }
}
