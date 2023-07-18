<?php

use Illuminate\Database\Eloquent\Model;
use Larastash\Reviews\Review;

if (!function_exists('review')) {
    /**
     * Get a new Review instance for the provided reviewable entity.
     *
     * @param Model $reviewable The reviewable entity for which the review is being created.
     * @return Review The Review instance for the provided reviewable entity.
     */
    function review(Model $reviewable): Review
    {
        return new Review($reviewable);
    }
}
