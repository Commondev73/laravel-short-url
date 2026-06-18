<?php

namespace App\Jobs;

use App\Services\ShortUrl\ShortUrlService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RecordShortUrlClick implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly int $shortUrlId
    ) {}

    public function handle(ShortUrlService $service): void
    {
        $service->clickCount($this->shortUrlId);
    }
}
