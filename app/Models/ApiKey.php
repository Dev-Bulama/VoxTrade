<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'service_name',
        'api_key',
        'api_secret',
        'extra_config',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'api_key'      => 'encrypted',
            'api_secret'   => 'encrypted',
            'extra_config' => 'array',
            'is_active'    => 'boolean',
        ];
    }

    // -------------------------------------------------------------------------
    // Static helpers
    // -------------------------------------------------------------------------

    /**
     * Retrieve the decrypted API key for a given service, or null if not found.
     */
    public static function getKey(string $service): ?string
    {
        $record = static::where('service_name', $service)
            ->where('is_active', true)
            ->first();

        return $record?->api_key;
    }
}
