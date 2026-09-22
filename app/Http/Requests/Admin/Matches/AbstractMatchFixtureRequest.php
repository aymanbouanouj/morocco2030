<?php

namespace App\Http\Requests\Admin\Matches;

use App\Models\MatchFixture;
use App\Models\Team;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Validation\Validator;

abstract class AbstractMatchFixtureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'stadium_id' => ['nullable', 'integer', 'exists:stadiums,id'],
            'city_id' => ['nullable', 'integer', 'exists:cities,id'],
            'group_id' => [
                Rule::requiredIf(fn () => $this->input('stage_type') === 'group'),
                'nullable',
                'integer',
                'exists:groups,id',
            ],
            'home_team_id' => ['nullable', 'integer', 'exists:teams,id', 'different:away_team_id'],
            'away_team_id' => ['nullable', 'integer', 'exists:teams,id'],
            'code' => ['required', 'string', 'max:32', $this->uniqueCodeRule()],
            'slug' => ['required', 'string', 'max:255', $this->uniqueSlugRule()],
            'stage_type' => ['required', Rule::in(MatchFixture::STAGE_TYPES)],
            'round_number' => ['nullable', 'integer', 'min:1', 'max:32'],
            'match_date' => ['required', 'date'],
            'timezone' => ['nullable', 'string', 'max:64'],
            'status' => ['required', Rule::in(MatchFixture::STATUSES)],
            'attendance' => ['nullable', 'integer', 'min:0'],
            'home_score' => ['nullable', 'integer', 'min:0', 'max:99'],
            'away_score' => ['nullable', 'integer', 'min:0', 'max:99'],
            'home_penalty_score' => ['nullable', 'integer', 'min:0', 'max:99'],
            'away_penalty_score' => ['nullable', 'integer', 'min:0', 'max:99'],
            'extra_time_played' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $stageType = $this->string('stage_type')->toString();
            $status = $this->string('status')->toString();
            $homeTeamId = $this->input('home_team_id');
            $awayTeamId = $this->input('away_team_id');
            $homeScore = $this->input('home_score');
            $awayScore = $this->input('away_score');
            $homePenaltyScore = $this->input('home_penalty_score');
            $awayPenaltyScore = $this->input('away_penalty_score');

            $requiresResolvedTeams = $stageType === 'group' || in_array($status, ['live', 'completed'], true);
            $isKnockoutStage = in_array($stageType, MatchFixture::KNOCKOUT_STAGE_TYPES, true);

            if ($requiresResolvedTeams && ! $homeTeamId) {
                $validator->errors()->add('home_team_id', 'The home team is required for group-stage, live, and completed fixtures.');
            }

            if ($requiresResolvedTeams && ! $awayTeamId) {
                $validator->errors()->add('away_team_id', 'The away team is required for group-stage, live, and completed fixtures.');
            }

            if (($homeTeamId || $awayTeamId) && (! $homeTeamId || ! $awayTeamId) && $stageType === 'group') {
                $validator->errors()->add('away_team_id', 'Group-stage fixtures require both teams to be assigned.');
            }

            if ($stageType !== 'group' && $this->filled('group_id')) {
                $validator->errors()->add('group_id', 'Only group-stage fixtures may be assigned to a group.');
            }

            if ($stageType === 'group' && $this->filled('group_id') && $homeTeamId && $awayTeamId) {
                $teamGroupIds = Team::query()
                    ->whereIn('id', [$homeTeamId, $awayTeamId])
                    ->pluck('group_id', 'id');

                if ((int) ($teamGroupIds[$homeTeamId] ?? 0) !== (int) $this->input('group_id')) {
                    $validator->errors()->add('home_team_id', 'The home team must belong to the selected group.');
                }

                if ((int) ($teamGroupIds[$awayTeamId] ?? 0) !== (int) $this->input('group_id')) {
                    $validator->errors()->add('away_team_id', 'The away team must belong to the selected group.');
                }
            }

            if (($homeScore !== null || $awayScore !== null) && (! $homeTeamId || ! $awayTeamId)) {
                $validator->errors()->add('home_score', 'Scores cannot be recorded until both teams are assigned.');
            }

            if ($status === 'completed') {
                if ($homeScore === null) {
                    $validator->errors()->add('home_score', 'A completed match requires a home score.');
                }

                if ($awayScore === null) {
                    $validator->errors()->add('away_score', 'A completed match requires an away score.');
                }
            }

            if (($homeScore === null) xor ($awayScore === null)) {
                $validator->errors()->add('away_score', 'Both home and away scores must be provided together.');
            }

            if (($homePenaltyScore !== null || $awayPenaltyScore !== null) && $status !== 'completed') {
                $validator->errors()->add('home_penalty_score', 'Penalty scores can only be recorded for completed matches.');
            }

            if (($homePenaltyScore !== null || $awayPenaltyScore !== null) && $stageType === 'group') {
                $validator->errors()->add('home_penalty_score', 'Penalty scores are not valid for group-stage fixtures.');
            }

            if (($homePenaltyScore === null) xor ($awayPenaltyScore === null)) {
                $validator->errors()->add('away_penalty_score', 'Both penalty scores must be provided together.');
            }

            if (
                $homePenaltyScore !== null
                && $awayPenaltyScore !== null
                && $homeScore !== null
                && $awayScore !== null
                && (int) $homeScore !== (int) $awayScore
            ) {
                $validator->errors()->add('home_penalty_score', 'Penalty scores are only valid when the main scoreline is tied.');
            }

            if (
                $status === 'completed'
                && $isKnockoutStage
                && $homeScore !== null
                && $awayScore !== null
                && (int) $homeScore === (int) $awayScore
                && (
                    $homePenaltyScore === null
                    || $awayPenaltyScore === null
                    || (int) $homePenaltyScore === (int) $awayPenaltyScore
                )
            ) {
                $validator->errors()->add('home_penalty_score', 'A completed knockout fixture requires a decisive winner, including penalty scores when the main scoreline is tied.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'group_id.required' => 'A group must be selected for group-stage fixtures.',
        ];
    }

    abstract protected function uniqueCodeRule(): Unique;

    abstract protected function uniqueSlugRule(): Unique;
}
