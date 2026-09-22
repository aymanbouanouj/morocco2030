<?php

namespace App\Support;

use Carbon\CarbonInterface;

class TournamentFormatting
{
    private const STAGE_LABELS = [
        'group' => 'Group Stage',
        'round_of_32' => 'Round of 32',
        'round_of_16' => 'Round of 16',
        'quarter_final' => 'Quarter-final',
        'semi_final' => 'Semi-final',
        'third_place' => 'Third Place Play-off',
        'final' => 'Final',
    ];

    private const STATUS_LABELS = [
        'scheduled' => 'Scheduled',
        'live' => 'Live',
        'completed' => 'Completed',
        'postponed' => 'Postponed',
        'cancelled' => 'Cancelled',
    ];

    private const PLAYER_POSITION_LABELS = [
        'goalkeeper' => 'Goalkeeper',
        'defender' => 'Defender',
        'midfielder' => 'Midfielder',
        'forward' => 'Forward',
    ];

    private const EVENT_TYPE_LABELS = [
        'goal' => 'Goal',
        'own_goal' => 'Own Goal',
        'yellow_card' => 'Yellow Card',
        'red_card' => 'Red Card',
        'substitution' => 'Substitution',
        'penalty' => 'Penalty',
        'var' => 'VAR',
        'kickoff' => 'Kick-off',
        'halftime' => 'Half-time',
        'fulltime' => 'Full-time',
    ];

    private const MATCH_CONTEXT_LABELS = [
        'full_time' => 'Full Time',
        'first_half' => 'First Half',
        'second_half' => 'Second Half',
        'extra_time' => 'Extra Time',
    ];

    private const STATISTIC_LABELS = [
        'possession_percentage' => 'Possession',
        'shots' => 'Shots',
        'shots_on_target' => 'Shots on Target',
        'passes' => 'Passes',
        'pass_accuracy' => 'Pass Accuracy',
        'fouls' => 'Fouls',
        'yellow_cards' => 'Yellow Cards',
        'red_cards' => 'Red Cards',
        'corners' => 'Corners',
        'offsides' => 'Offsides',
        'saves' => 'Saves',
    ];

    public static function stageLabel(?string $stageType): string
    {
        $label = self::STAGE_LABELS[$stageType] ?? str((string) $stageType)->replace('_', ' ')->title()->toString();

        return __($label);
    }

    public static function statusLabel(?string $status): string
    {
        $label = self::STATUS_LABELS[$status] ?? str((string) $status)->replace('_', ' ')->title()->toString();

        return __($label);
    }

    public static function playerPositionLabel(?string $position): ?string
    {
        if (! $position) {
            return null;
        }

        $label = self::PLAYER_POSITION_LABELS[$position] ?? str($position)->replace('_', ' ')->title()->toString();

        return __($label);
    }

    public static function eventTypeLabel(?string $eventType): ?string
    {
        if (! $eventType) {
            return null;
        }

        $label = self::EVENT_TYPE_LABELS[$eventType] ?? str($eventType)->replace('_', ' ')->title()->toString();

        return __($label);
    }

    public static function matchContextLabel(?string $context): ?string
    {
        if (! $context) {
            return null;
        }

        $label = self::MATCH_CONTEXT_LABELS[$context] ?? str($context)->replace('_', ' ')->title()->toString();

        return __($label);
    }

    public static function statisticLabel(?string $metricKey): ?string
    {
        if (! $metricKey) {
            return null;
        }

        $label = self::STATISTIC_LABELS[$metricKey] ?? str($metricKey)->replace('_', ' ')->title()->toString();

        return __($label);
    }

    public static function matchDate(?CarbonInterface $date, ?string $timezone = null, string $format = 'M j, Y g:i A'): ?string
    {
        if (! $date) {
            return null;
        }

        return $date->copy()
            ->timezone($timezone ?: config('app.timezone'))
            ->translatedFormat($format);
    }
}
