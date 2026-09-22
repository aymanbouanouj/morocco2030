<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_name',
        'setting_key',
        'value',
        'type',
        'is_public',
        'autoload',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
            'autoload' => 'boolean',
        ];
    }

    public function isSensitive(): bool
    {
        return Str::contains(Str::lower($this->group_name.'.'.$this->setting_key), [
            'app_key',
            'api_key',
            'password',
            'secret',
            'token',
            'credential',
            'private',
            'db_',
            'database',
            'mail_password',
            'smtp_password',
        ]);
    }

    public function displayValue(): string
    {
        if ($this->isSensitive()) {
            return '[protected]';
        }

        if ($this->value === null || $this->value === '') {
            return 'Not set';
        }

        return Str::limit((string) $this->value, 80);
    }
}
