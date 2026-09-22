<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitorAnalytic extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'path',
        'route_name',
        'referrer',
        'ip_address',
        'user_agent',
        'country_code',
        'city_name',
        'language_code',
        'device_type',
        'event_type',
        'event_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'event_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
