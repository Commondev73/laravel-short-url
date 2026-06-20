<?php

namespace App\Services\User;

use App\Models\User;
use App\Repositories\Cache\CacheRepositoryInterface;
use Closure;

class UserCacheService
{
    private const TTL_SECONDS = 3600;

    private const KEY_PREFIX_ID = 'user:id:';

    public function __construct(
        private CacheRepositoryInterface $cache
    ) {}

    public function rememberById(int $id, Closure $callback): ?User
    {
        $data = $this->cache->remember(
            self::KEY_PREFIX_ID . $id,
            self::TTL_SECONDS,
            $callback
        );

        return $this->hydrate($data);
    }

    public function forget(User $user): void
    {
        $this->cache->forget(self::KEY_PREFIX_ID . $user->id);
    }

    private function hydrate(?array $data): ?User
    {
        if ($data === null) {
            return null;
        }

        return User::hydrate([$data])->first();
    }
}
