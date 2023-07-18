<?php

namespace Larastash\Reviews\Concerns;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Larastash\Reviews\Models\Review;

trait Reviewer
{
    /**
     * Get all the reviews written by the reviewer.
     *
     * @return HasMany
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'user_id', $this->getKeyName());
    }
}
