<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Subscription extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'plan',
        'amount',
        'currency',
        'expires_at',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'amount'     => 'decimal:2',
        ];
    }

    // -------------------------------------------------------------------------
    // Relationships
    // -------------------------------------------------------------------------

    /**
     * Get the user that owns this subscription.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Scope a query to only include active, non-expired subscriptions.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')
                     ->where(function (Builder $q) {
                         $q->whereNull('expires_at')
                           ->orWhere('expires_at', '>', Carbon::now());
                     });
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    /**
     * Determine whether this subscription has expired.
     */
    public function getIsExpiredAttribute(): bool
    {
        if ($this->status !== 'active') {
            return true;
        }

        if ($this->expires_at === null) {
            return false;
        }

        return $this->expires_at->isPast();
    }
}
