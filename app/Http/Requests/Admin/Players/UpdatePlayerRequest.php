<?php

namespace App\Http\Requests\Admin\Players;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePlayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $playerId = $this->route('player')->id;

        return [
            'team_id' => ['required', 'integer', 'exists:teams,id'],
            'display_name' => ['required', 'string', 'max:255'],
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('players', 'slug')->ignore($playerId)],
            'shirt_number' => ['nullable', 'integer', 'min:1', 'max:99'],
            'position' => ['required', Rule::in(['goalkeeper', 'defender', 'midfielder', 'forward'])],
            'date_of_birth' => ['nullable', 'date'],
            'nationality_code' => ['nullable', 'string', 'size:2'],
            'height_cm' => ['nullable', 'integer', 'min:100', 'max:250'],
            'weight_kg' => ['nullable', 'integer', 'min:30', 'max:200'],
            'bio' => ['nullable', 'string'],
            'is_captain' => ['nullable', 'boolean'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }
}
