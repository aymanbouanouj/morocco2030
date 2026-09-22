<?php

namespace App\Models\Concerns;

use App\Models\EditorialWorkflow;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasEditorialWorkflows
{
    public function editorialWorkflows(): MorphMany
    {
        return $this->morphMany(EditorialWorkflow::class, 'workflowable');
    }
}
