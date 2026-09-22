<?php

namespace App\Models;

use App\Models\Concerns\HasMediaRelations;
use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Partner extends Model
{
    use HasFactory, HasMediaRelations, HasTranslations, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'category',
        'tier',
        'website_url',
        'logo_path',
        'logo_alt',
        'contact_email',
        'contact_phone',
        'description',
        'status',
    ];

    public function scopePubliclyVisible(Builder $query): Builder
    {
        return $query->where('status', 'active');
    }

    public function scopePublicOrder(Builder $query): Builder
    {
        return $query
            ->orderByRaw("CASE WHEN tier IS NULL OR tier = '' THEN 1 ELSE 0 END")
            ->orderBy('tier')
            ->orderBy('name');
    }

    public function hasLogo(): bool
    {
        return is_string($this->logo_path) && $this->logo_path !== '';
    }

    public function logoUrl(): ?string
    {
        if (! $this->hasLogo()) {
            return null;
        }

        return Storage::disk('public')->url($this->logo_path);
    }

    public function logoAlt(?string $fallback = null): string
    {
        return $this->logo_alt ?: $fallback ?: $this->name;
    }
}
