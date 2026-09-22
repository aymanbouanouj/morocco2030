<?php

namespace App\Http\Requests\Admin\MatchEvents;

use App\Models\MatchEvent;
use App\Models\MatchFixture;
use App\Models\Player;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

abstract class AbstractMatchEventRequest extends FormRequest
{
    private const TEAM_REQUIRED_EVENT_TYPES = [
        'goal',
        'own_goal',
        'yellow_card',
        'red_card',
        'substitution',
        'penalty',
    ];

    private const PLAYER_REQUIRED_EVENT_TYPES = [
        'goal',
        'own_goal',
        'yellow_card',
        'red_card',
        'substitution',
        'penalty',
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'team_id' => ['nullable', 'integer', 'exists:teams,id'],
            'player_id' => ['nullable', 'integer', 'exists:players,id'],
            'related_player_id' => ['nullable', 'integer', 'exists:players,id'],
            'minute' => ['required', 'integer', 'min:0', 'max:130'],
            'extra_minute' => ['nullable', 'integer', 'min:0', 'max:30'],
            'period' => ['nullable', Rule::in(MatchEvent::PERIODS)],
            'event_type' => ['required', Rule::in(MatchEvent::EVENT_TYPES)],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $match = $this->matchFixture();
            $teamId = $this->integer('team_id') ?: null;
            $playerId = $this->integer('player_id') ?: null;
            $relatedPlayerId = $this->integer('related_player_id') ?: null;
            $eventType = $this->string('event_type')->toString();

            if ($teamId && ! $this->teamBelongsToMatch($match, $teamId)) {
                $validator->errors()->add('team_id', 'The selected team must be one of the fixture teams.');
            }

            if (in_array($eventType, self::TEAM_REQUIRED_EVENT_TYPES, true) && ! $teamId) {
                $validator->errors()->add('team_id', 'The selected event type requires a team.');
            }

            if (in_array($eventType, self::PLAYER_REQUIRED_EVENT_TYPES, true) && ! $playerId) {
                $validator->errors()->add('player_id', 'The selected event type requires a player.');
            }

            if ($playerId) {
                $player = Player::query()->find($playerId);

                if (! $teamId) {
                    $validator->errors()->add('team_id', 'Select a team before assigning a player.');
                } elseif ($player && $player->team_id !== $teamId) {
                    $validator->errors()->add('player_id', 'The selected player does not belong to the selected team.');
                }
            }

            if ($eventType === 'substitution' && ! $relatedPlayerId) {
                $validator->errors()->add('related_player_id', 'A substitution event requires the related player field.');
            }

            if ($relatedPlayerId) {
                $relatedPlayer = Player::query()->find($relatedPlayerId);

                if ($eventType !== 'substitution') {
                    $validator->errors()->add('related_player_id', 'The related player field is only used for substitutions.');
                } elseif (! $teamId) {
                    $validator->errors()->add('team_id', 'Select a team before assigning a related player.');
                } elseif ($relatedPlayer && $relatedPlayer->team_id !== $teamId) {
                    $validator->errors()->add('related_player_id', 'The related player must belong to the selected team.');
                }
            }
        });
    }

    protected function matchFixture(): MatchFixture
    {
        /** @var MatchFixture $match */
        $match = $this->route('match');

        return $match;
    }

    protected function teamBelongsToMatch(MatchFixture $match, int $teamId): bool
    {
        return in_array($teamId, [
            $match->home_team_id,
            $match->away_team_id,
        ], true);
    }
}
