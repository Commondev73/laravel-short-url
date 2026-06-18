<?php

namespace App\Providers;

use App\Repositories\Eloquent\RefreshTokenRepository;
use App\Repositories\Eloquent\ShortUrlRepository;
use App\Repositories\Eloquent\UserRepository;
use App\Repositories\Cache\CacheRepository;
use App\Repositories\Cache\CacheRepositoryInterface;
use App\Repositories\Interfaces\RefreshTokenRepositoryInterface;
use App\Repositories\Interfaces\ShortUrlRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(RefreshTokenRepositoryInterface::class, RefreshTokenRepository::class);
        $this->app->bind(ShortUrlRepositoryInterface::class, ShortUrlRepository::class);
        $this->app->bind(CacheRepositoryInterface::class, CacheRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
