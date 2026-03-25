<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TableToken extends Model
{
    protected $fillable = [
        'table_number',
        'token',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Check if the token is still valid (active and not expired).
     */
    public function isValid(): bool
    {
        return $this->is_active && $this->expires_at->isFuture();
    }

    /**
     * Find a valid token by token string.
     */
    public static function findValidToken(string $token): ?self
    {
        return static::where('token', $token)
            ->where('is_active', true)
            ->where('expires_at', '>', now())
            ->first();
    }
}
