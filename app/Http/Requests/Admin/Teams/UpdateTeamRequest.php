<?php

namespace App\Http\Requests\Admin\Teams;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $teamId = $this->route('team')->id;

        return [
            'group_id' => ['required', 'integer', 'exists:groups,id'],
            'name' => ['required', 'string', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:16', Rule::unique('teams', 'code')->ignore($teamId)],
            'slug' => ['required', 'string', 'max:255', Rule::unique('teams', 'slug')->ignore($teamId)],
            'federation_name' => ['nullable', 'string', 'max:255'],
            'founded_year' => ['nullable', 'integer', 'min:1800', 'max:2100'],
            'coach_name' => ['nullable', 'string', 'max:255'],
            'team_type' => ['required', Rule::in(['national', 'club'])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }
}
