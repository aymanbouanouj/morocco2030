<?php

namespace App\Http\Requests\Admin\Settings;

use App\Models\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var Setting|null $setting */
        $setting = $this->route('setting');

        $valueRules = match ($setting?->type) {
            'boolean', 'bool' => ['nullable', Rule::in(['0', '1', 'true', 'false'])],
            'integer', 'int' => ['nullable', 'integer'],
            'json', 'array' => ['nullable', 'json'],
            default => ['nullable', 'string', 'max:10000'],
        };

        return [
            'value' => $valueRules,
            'is_public' => ['nullable', 'boolean'],
            'autoload' => ['nullable', 'boolean'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
