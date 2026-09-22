<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SportsAnalyticsSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'analyzable_type',
        'analyzable_id',
        'metric_key',
        'snapshot_date',
        'metric_value',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'snapshot_date' => 'date',
            'metric_value' => 'decimal:4',
            'payload' => 'array',
        ];
    }

    public function analyzable(): MorphTo
    {
        return $this->morphTo();
    }
}
