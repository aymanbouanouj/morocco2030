<?php

namespace App\Http\Requests\Admin\Matches;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class StoreMatchFixtureRequest extends AbstractMatchFixtureRequest
{
    protected function uniqueCodeRule(): Unique
    {
        return Rule::unique('matches', 'code');
    }

    protected function uniqueSlugRule(): Unique
    {
        return Rule::unique('matches', 'slug');
    }
}
