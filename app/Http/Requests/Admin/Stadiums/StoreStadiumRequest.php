<?php

namespace App\Http\Requests\Admin\Stadiums;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreStadiumRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('stadiums', 'slug')],
            'code' => ['nullable', 'string', 'max:32', Rule::unique('stadiums', 'code')],
            'capacity' => ['nullable', 'integer', 'min:0'],
            'address' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'opened_year' => ['nullable', 'integer', 'min:1800', 'max:2100'],
            'surface_type' => ['nullable', 'string', 'max:32'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }
}
