<?php

namespace App\Models\Concerns;

use App\Models\MediaRelation;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasMediaRelations
{
    public function mediaRelations(): MorphMany
    {
        return $this->morphMany(MediaRelation::class, 'mediable');
    }
}
