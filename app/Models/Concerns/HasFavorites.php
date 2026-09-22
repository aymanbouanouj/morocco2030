<?php

namespace App\Models\Concerns;

use App\Models\UserFavorite;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasFavorites
{
    public function favorites(): MorphMany
    {
        return $this->morphMany(UserFavorite::class, 'favorable');
    }
}
