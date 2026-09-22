<?php

namespace App\Http\Requests\Site\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['nullable', 'string', 'max:30', Rule::unique('users', 'phone')],
            'preferred_locale' => [
                'nullable',
                'string',
                'max:10',
                Rule::exists('languages', 'code')->where('is_active', true),
            ],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
