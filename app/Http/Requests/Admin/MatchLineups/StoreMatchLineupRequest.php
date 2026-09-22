<?php

namespace App\Http\Requests\Admin\MatchLineups;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class StoreMatchLineupRequest extends AbstractMatchLineupRequest
{
    protected function uniqueLineupRule(): Unique
    {
        return Rule::unique('match_lineups')->where(function ($query) {
            $query->where('match_id', $this->matchFixture()->id)
                ->where('team_id', $this->input('team_id'));
        });
    }
}
