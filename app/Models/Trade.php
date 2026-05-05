<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'pair',
        'type',
        'entry_price',
        'stop_loss',
        'take_profit',
        'confidence',
        'duration',
        'category',
        'risk_level',
        'status',
        'analysis_summary',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'entry_price' => 'decimal:8',
            'stop_loss'   => 'decimal:8',
            'take_profit' => 'decimal:8',
            'confidence'  => 'integer',
        ];
    }

    // -------------------------------------------------------------------------
    // Scopes
    // -------------------------------------------------------------------------

    /**
     * Scope a query to only include active trades.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include forex trades.
     */
    public function scopeForex(Builder $query): Builder
    {
        return $query->where('category', 'forex');
    }

    /**
     * Scope a query to only include crypto trades.
     */
    public function scopeCrypto(Builder $query): Builder
    {
        return $query->where('category', 'crypto');
    }

    // -------------------------------------------------------------------------
    // Accessors
    // -------------------------------------------------------------------------

    /**
     * Calculate the profit potential as a percentage of the entry price.
     */
    public function getProfitPotentialAttribute(): float
    {
        if ((float) $this->entry_price == 0) {
            return 0.0;
        }

        return ((float) $this->take_profit - (float) $this->entry_price)
            / (float) $this->entry_price
            * 100;
    }
}
