<?php

namespace App\Http\Requests\Admin\Matches;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class UpdateMatchFixtureRequest extends AbstractMatchFixtureRequest
{
    protected function uniqueCodeRule(): Unique
    {
        $matchId = $this->route('match')->id;

        return Rule::unique('matches', 'code')->ignore($matchId);
    }

    protected function uniqueSlugRule(): Unique
    {
        $matchId = $this->route('match')->id;

        return Rule::unique('matches', 'slug')->ignore($matchId);
    }
}
