<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchStatistic extends Model
{
    use HasFactory;

    public const METRIC_KEYS = [
        'possession_percentage',
        'shots',
        'shots_on_target',
        'passes',
        'pass_accuracy',
        'fouls',
        'yellow_cards',
        'red_cards',
        'corners',
        'offsides',
        'saves',
    ];

    public const CONTEXTS = [
        'full_time',
        'first_half',
        'second_half',
        'extra_time',
        'penalties',
    ];

    protected $fillable = [
        'match_id',
        'team_id',
        'metric_key',
        'metric_value',
        'display_value',
        'context',
    ];

    protected function casts(): array
    {
        return [
            'metric_value' => 'decimal:4',
        ];
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(MatchFixture::class, 'match_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function formattedMetricValue(): string
    {
        if ($this->display_value) {
            return $this->display_value;
        }

        if ($this->metric_value === null) {
            return 'N/A';
        }

        return rtrim(rtrim((string) $this->metric_value, '0'), '.');
    }
}
