<?php

namespace App\Http\Requests\Admin\MediaFiles;

use App\Models\City;
use App\Models\News;
use App\Models\Partner;
use App\Models\Player;
use App\Models\Stadium;
use App\Models\Team;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class AttachMediaFileRequest extends FormRequest
{
    /**
     * @return array<string, array{model: class-string<Model>, label: string, role: string, order: string}>
     */
    public static function targetMap(): array
    {
        return [
            'team' => [
                'model' => Team::class,
                'label' => 'Teams',
                'role' => 'logo',
                'order' => 'name',
            ],
            'player' => [
                'model' => Player::class,
                'label' => 'Players',
                'role' => 'photo',
                'order' => 'display_name',
            ],
            'city' => [
                'model' => City::class,
                'label' => 'Cities',
                'role' => 'image',
                'order' => 'name',
            ],
            'stadium' => [
                'model' => Stadium::class,
                'label' => 'Stadiums',
                'role' => 'image',
                'order' => 'name',
            ],
            'news' => [
                'model' => News::class,
                'label' => 'News',
                'role' => 'cover',
                'order' => 'title',
            ],
            'partner' => [
                'model' => Partner::class,
                'label' => 'Partners',
                'role' => 'logo',
                'order' => 'name',
            ],
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function allowedRoles(): array
    {
        return array_values(array_unique(array_column(self::targetMap(), 'role')));
    }

    public static function roleForTarget(string $targetType): ?string
    {
        return self::targetMap()[$targetType]['role'] ?? null;
    }

    /**
     * @return class-string<Model>|null
     */
    public static function modelForTarget(string $targetType): ?string
    {
        return self::targetMap()[$targetType]['model'] ?? null;
    }

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $targetRef = (string) $this->input('target_ref', '');

        if ($targetRef !== '' && str_contains($targetRef, ':')) {
            [$targetType, $targetId] = explode(':', $targetRef, 2);

            $this->merge([
                'target_type' => $this->input('target_type') ?: $targetType,
                'target_id' => $this->input('target_id') ?: $targetId,
            ]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'target_ref' => ['nullable', 'string', 'max:80'],
            'target_type' => ['required', 'string', Rule::in(array_keys(self::targetMap()))],
            'target_id' => ['required', 'integer', 'min:1'],
            'role' => ['required', 'string', Rule::in(self::allowedRoles())],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $targetType = (string) $this->input('target_type');
                $targetId = (int) $this->input('target_id');
                $role = (string) $this->input('role');
                $expectedRole = self::roleForTarget($targetType);
                $modelClass = self::modelForTarget($targetType);

                if ($expectedRole !== null && $role !== $expectedRole) {
                    $validator->errors()->add('role', 'The selected role is not allowed for this target type.');
                }

                if ($modelClass !== null && $targetId > 0 && ! $modelClass::query()->whereKey($targetId)->exists()) {
                    $validator->errors()->add('target_id', 'The selected target record does not exist.');
                }
            },
        ];
    }

    public function targetModel(): Model
    {
        $targetType = (string) $this->validated('target_type');
        $targetId = (int) $this->validated('target_id');
        $modelClass = self::modelForTarget($targetType);

        abort_if($modelClass === null, 422);

        return $modelClass::query()->findOrFail($targetId);
    }
}
