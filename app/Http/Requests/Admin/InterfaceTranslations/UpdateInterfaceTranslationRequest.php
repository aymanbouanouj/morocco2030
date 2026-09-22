<?php

namespace App\Http\Requests\Admin\InterfaceTranslations;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInterfaceTranslationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'value' => ['nullable', 'string', 'max:20000'],
        ];
    }
}
