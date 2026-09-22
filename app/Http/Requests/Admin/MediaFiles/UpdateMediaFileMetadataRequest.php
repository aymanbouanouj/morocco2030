<?php

namespace App\Http\Requests\Admin\MediaFiles;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMediaFileMetadataRequest extends FormRequest
{
    /**
     * @return array<int, string>
     */
    public static function allowedVisibilities(): array
    {
        return [
            'public',
            'private',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'alt_text' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:1000'],
            'visibility' => ['nullable', 'string', Rule::in(self::allowedVisibilities())],
        ];
    }
}
