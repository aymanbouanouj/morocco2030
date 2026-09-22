<?php

namespace App\Http\Requests\Admin\MediaFiles;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class StoreMediaFileRequest extends FormRequest
{
    public const MAX_UPLOAD_KILOBYTES = 4096;

    /**
     * @return array<int, string>
     */
    public static function allowedCategories(): array
    {
        return [
            'teams',
            'players',
            'cities',
            'stadiums',
            'news',
            'partners',
            'generic',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                File::image()
                    ->types(['jpg', 'jpeg', 'png', 'webp'])
                    ->max(self::MAX_UPLOAD_KILOBYTES),
                'extensions:jpg,jpeg,png,webp',
            ],
            'category' => ['required', Rule::in(self::allowedCategories())],
            'title' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
