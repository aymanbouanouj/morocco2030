<?php

namespace App\Http\Requests\Admin\Teams;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'group_id' => ['required', 'integer', 'exists:groups,id'],
            'name' => ['required', 'string', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:16', Rule::unique('teams', 'code')],
            'slug' => ['required', 'string', 'max:255', Rule::unique('teams', 'slug')],
            'federation_name' => ['nullable', 'string', 'max:255'],
            'founded_year' => ['nullable', 'integer', 'min:1800', 'max:2100'],
            'coach_name' => ['nullable', 'string', 'max:255'],
            'team_type' => ['required', Rule::in(['national', 'club'])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }
}
