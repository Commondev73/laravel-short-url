<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

#[Fillable(['user_id', 'token_hash', 'expires_at', 'revoked_at'])]
#[Hidden(['token_hash'])]
class RefreshToken extends Model
{
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->whereNull('revoked_at')
            ->where('expires_at', '>', now());
    }

    public function isExpired(): bool
    {
        $expiresAt = $this->expires_at;

        if (! $expiresAt instanceof Carbon) {
            return true;
        }

        return $expiresAt->isPast();
    }

    public function isRevoked(): bool
    {
        return $this->revoked_at !== null;
    }

    public function isActive(): bool
    {
        return ! $this->isExpired() && ! $this->isRevoked();
    }
}
