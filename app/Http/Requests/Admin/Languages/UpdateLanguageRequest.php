<?php

namespace App\Http\Requests\Admin\Languages;

use App\Models\Language;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLanguageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'native_name' => ['required', 'string', 'max:255'],
            'direction' => ['required', Rule::in(['ltr', 'rtl'])],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:65535'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            /** @var Language|null $language */
            $language = $this->route('language');

            if ($language?->is_default && ! $this->boolean('is_active')) {
                $validator->errors()->add('is_active', 'The default language cannot be deactivated.');
            }
        });
    }
}
