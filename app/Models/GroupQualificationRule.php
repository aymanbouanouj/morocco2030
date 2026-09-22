<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupQualificationRule extends Model
{
    use HasFactory;

    public const TEAM_SLOTS = [
        'home',
        'away',
    ];

    protected $fillable = [
        'group_id',
        'qualifying_position',
        'target_match_id',
        'team_slot',
        'label',
        'applied_team_id',
        'applied_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'applied_at' => 'datetime',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function targetMatch(): BelongsTo
    {
        return $this->belongsTo(MatchFixture::class, 'target_match_id');
    }

    public function appliedTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'applied_team_id');
    }

    public function slotColumn(): ?string
    {
        return match ($this->team_slot) {
            'home' => 'home_team_id',
            'away' => 'away_team_id',
            default => null,
        };
    }

    public function publicLabel(): string
    {
        if ($this->label) {
            return $this->label;
        }

        $groupLabel = $this->group?->name ?? __('Group :code', ['code' => $this->group?->code ?? '?']);

        return match ((int) $this->qualifying_position) {
            1 => __('Winner of :group', ['group' => $groupLabel]),
            2 => __('Runner-up of :group', ['group' => $groupLabel]),
            3 => __('Third place of :group', ['group' => $groupLabel]),
            default => __('Qualifier :position of :group', [
                'position' => $this->qualifying_position,
                'group' => $groupLabel,
            ]),
        };
    }
}
