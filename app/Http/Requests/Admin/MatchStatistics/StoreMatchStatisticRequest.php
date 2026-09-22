<?php

namespace App\Http\Requests\Admin\MatchStatistics;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class StoreMatchStatisticRequest extends AbstractMatchStatisticRequest
{
    protected function uniqueStatisticRule(): Unique
    {
        return Rule::unique('match_statistics')->where(function ($query) {
            $query->where('match_id', $this->matchFixture()->id)
                ->where('team_id', $this->input('team_id'))
                ->where('context', $this->input('context'));
        });
    }
}
