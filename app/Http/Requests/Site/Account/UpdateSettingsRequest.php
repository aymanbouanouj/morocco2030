<?php

namespace App\Http\Requests\Site\Account;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->canAccessPublicAccount() ?? false;
    }

    public function rules(): array
    {
        return [
            'preferred_locale' => [
                'nullable',
                'string',
                'max:10',
                Rule::exists('languages', 'code')->where('is_active', true),
            ],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }
}
