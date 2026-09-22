<?php

namespace App\Http\Requests\Admin\News;

use App\Support\NewsMediaStorage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateNewsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('news'));
    }

    public function rules(): array
    {
        $newsId = $this->route('news')->id;

        $rules = [
            'category_id' => ['required', 'integer', 'exists:news_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('news', 'slug')->ignore($newsId)],
            'summary' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'visibility' => ['required', Rule::in(['public', 'private'])],
            'featured_at' => ['nullable', 'date'],
            'published_at' => ['nullable', 'date'],
            'status' => ['prohibited'],
        ];

        if (! $this->user()->hasPermission('news.publish')) {
            $rules['featured_at'] = ['prohibited'];
            $rules['published_at'] = ['prohibited'];
        }

        return array_merge($rules, NewsMediaStorage::mediaValidationRules(isUpdate: true));
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $news = $this->route('news');
            $remove = (array) $this->input('remove_gallery_paths', []);
            $allowed = $news?->gallery_image_paths ?? [];

            foreach ($remove as $index => $path) {
                if (! is_string($path) || ! in_array($path, $allowed, true)) {
                    $validator->errors()->add("remove_gallery_paths.{$index}", 'Invalid gallery image selection.');
                }
            }

            $remaining = count(array_values(array_filter(
                $allowed,
                fn (string $path) => ! in_array($path, $remove, true)
            )));
            $incoming = $this->hasFile('gallery_images') ? count($this->file('gallery_images')) : 0;

            if ($remaining + $incoming > 8) {
                $validator->errors()->add('gallery_images', 'A news article may have at most 8 gallery images.');
            }
        });
    }
}
