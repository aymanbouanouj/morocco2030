<?php

namespace App\Http\Requests\Admin\MatchLineups;

use App\Models\MatchFixture;
use App\Models\MatchLineup;
use App\Models\Player;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Validation\Validator;

abstract class AbstractMatchLineupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'team_id' => ['required', 'integer', 'exists:teams,id'],
            'player_id' => ['required', 'integer', 'exists:players,id', $this->uniqueLineupRule()],
            'lineup_type' => ['required', Rule::in(MatchLineup::LINEUP_TYPES)],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999'],
            'position' => ['nullable', 'string', 'max:32'],
            'shirt_number' => ['nullable', 'integer', 'min:1', 'max:99'],
            'formation_slot' => ['nullable', 'string', 'max:32'],
            'is_captain' => ['nullable', 'boolean'],
            'is_goalkeeper' => ['nullable', 'boolean'],
            'minute_in' => ['nullable', 'integer', 'min:0', 'max:130'],
            'minute_out' => ['nullable', 'integer', 'min:0', 'max:130'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $match = $this->matchFixture();
            $teamId = $this->integer('team_id');
            $playerId = $this->integer('player_id');
            $minuteIn = $this->integer('minute_in') ?: null;
            $minuteOut = $this->integer('minute_out') ?: null;

            if (! in_array($teamId, [$match->home_team_id, $match->away_team_id], true)) {
                $validator->errors()->add('team_id', 'The selected team must be one of the fixture teams.');
            }

            $player = Player::query()->find($playerId);

            if ($player && $player->team_id !== $teamId) {
                $validator->errors()->add('player_id', 'The selected player does not belong to the selected team.');
            }

            if ($minuteIn !== null && $minuteOut !== null && $minuteOut < $minuteIn) {
                $validator->errors()->add('minute_out', 'The minute out value must be greater than or equal to minute in.');
            }
        });
    }

    abstract protected function uniqueLineupRule(): Unique;

    protected function matchFixture(): MatchFixture
    {
        /** @var MatchFixture $match */
        $match = $this->route('match');

        return $match;
    }
}
