<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

#[Fillable([
    'user_id',
    'original_url',
    'short_code',
    'title',
    'click_count',
    'is_active',
    'expires_at',
])]
class ShortUrl extends Model
{
    protected $appends = ['short_url'];

    protected function casts(): array
    {
        return [
            'click_count' => 'integer',
            'is_active' => 'boolean',
            'expires_at' => 'datetime',
        ];
    }

    protected function shortUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->short_code !== null
                ? url("/api/short-urls/{$this->short_code}")
                : null,
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function isExpired(): bool
    {
        $expiresAt = $this->expires_at;

        if (! $expiresAt instanceof Carbon) {
            return false;
        }

        return $expiresAt->isPast();
    }

    public function isAccessible(): bool
    {
        return $this->is_active && ! $this->isExpired();
    }
}
