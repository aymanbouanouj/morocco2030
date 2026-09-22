<?php

namespace App\Http\Requests\Admin\MatchStatistics;

use App\Models\MatchStatistic;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class UpdateMatchStatisticRequest extends AbstractMatchStatisticRequest
{
    protected function uniqueStatisticRule(): Unique
    {
        /** @var MatchStatistic $statistic */
        $statistic = $this->route('statistic');

        return Rule::unique('match_statistics')->ignore($statistic->id)->where(function ($query) {
            $query->where('match_id', $this->matchFixture()->id)
                ->where('team_id', $this->input('team_id'))
                ->where('context', $this->input('context'));
        });
    }
}
