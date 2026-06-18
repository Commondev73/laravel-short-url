<?php

namespace App\Repositories\Cache;

use Closure;

interface CacheRepositoryInterface
{
    public function get(string $key): mixed;

    public function set(string $key, mixed $value, int $ttlSeconds): void;

    public function forget(string $key): void;

    public function remember(string $key, int $ttlSeconds, Closure $callback): mixed;
}
