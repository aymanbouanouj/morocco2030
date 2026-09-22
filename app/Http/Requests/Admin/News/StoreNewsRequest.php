<?php

namespace App\Http\Requests\Admin\News;

use App\Models\News;
use App\Support\NewsMediaStorage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreNewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', News::class);
    }

    public function rules(): array
    {
        $rules = [
            'category_id' => ['required', 'integer', 'exists:news_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('news', 'slug')],
            'summary' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'visibility' => ['required', Rule::in(['public', 'private'])],
            'featured_at' => ['nullable', 'date'],
            'published_at' => ['nullable', 'date'],
        ];

        if (! $this->user()->hasPermission('news.publish')) {
            $rules['featured_at'] = ['prohibited'];
            $rules['published_at'] = ['prohibited'];
        }

        return array_merge($rules, NewsMediaStorage::mediaValidationRules());
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if ($this->hasFile('gallery_images') && count($this->file('gallery_images')) > 8) {
                $validator->errors()->add('gallery_images', 'A news article may have at most 8 gallery images.');
            }
        });
    }
}
