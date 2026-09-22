<?php

namespace App\Http\Requests\Admin\MatchStatistics;

use App\Models\MatchFixture;
use App\Models\MatchStatistic;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Validation\Validator;

abstract class AbstractMatchStatisticRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'team_id' => ['required', 'integer', 'exists:teams,id'],
            'metric_key' => [
                'required',
                Rule::in(MatchStatistic::METRIC_KEYS),
                $this->uniqueStatisticRule(),
            ],
            'metric_value' => ['nullable', 'numeric', 'min:0', 'max:1000'],
            'display_value' => ['nullable', 'string', 'max:255'],
            'context' => ['required', Rule::in(MatchStatistic::CONTEXTS)],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $match = $this->matchFixture();
            $teamId = $this->integer('team_id');
            $metricKey = $this->string('metric_key')->toString();
            $metricValue = $this->input('metric_value');
            $displayValue = $this->string('display_value')->toString();

            if (! in_array($teamId, [$match->home_team_id, $match->away_team_id], true)) {
                $validator->errors()->add('team_id', 'The selected team must be one of the fixture teams.');
            }

            if ($metricValue === null && $displayValue === '') {
                $validator->errors()->add('metric_value', 'Provide either a numeric value or a display value.');
            }

            if (
                in_array($metricKey, ['possession_percentage', 'pass_accuracy'], true)
                && $metricValue !== null
                && (float) $metricValue > 100
            ) {
                $validator->errors()->add('metric_value', 'Percentage-based metrics cannot exceed 100.');
            }
        });
    }

    abstract protected function uniqueStatisticRule(): Unique;

    protected function matchFixture(): MatchFixture
    {
        /** @var MatchFixture $match */
        $match = $this->route('match');

        return $match;
    }
}
