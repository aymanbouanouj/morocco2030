<?php

namespace App\Http\Requests\Admin\MatchLineups;

use App\Models\MatchLineup;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class UpdateMatchLineupRequest extends AbstractMatchLineupRequest
{
    protected function uniqueLineupRule(): Unique
    {
        /** @var MatchLineup $lineup */
        $lineup = $this->route('lineup');

        return Rule::unique('match_lineups')->ignore($lineup->id)->where(function ($query) {
            $query->where('match_id', $this->matchFixture()->id)
                ->where('team_id', $this->input('team_id'));
        });
    }
}
