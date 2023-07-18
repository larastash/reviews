<?php

namespace Larastash\Reviews\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;
use Larastash\Reviews\Models\Review;
use Larastash\Reviews\Review as ReviewService;

trait Reviewable
{
    /**
     * Get all the reviews for the reviewable entity.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /**
     * Create or update a review for the reviewable entity.
     *
     * @param int|null $value The numeric value of the review.
     * @param string|null $body The body text of the review.
     * @param string|null $title The title of the review.
     * @param array $extra Extra data associated with the review.
     * @param int|null $userId The user ID associated with the review.
     *
     * @return \Larastash\Reviews\Models\Review|\Larastash\Reviews\Review
     */
    public function review(int|null $value = null, string|null $body = null, string|null $title = null, array $extra = [], int|null $userId = null): Review|ReviewService
    {
        if ($value === null) {
            return new ReviewService($this);
        }

        return $this->reviews()->updateOrCreate([
            'user_id' => $userId ?? Auth::id(),
        ], [
            ...compact('value', 'body', 'title', 'extra'),
        ]);
    }

    /**
     * Scope a query to include the average value of reviews.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeWithReviewAvgValue(Builder $query): Builder
    {
        return $query->withAvg('reviews', 'value');
    }

    /**
     * Scope a query to order by the average review value in ascending order.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeOrderByReviewValue(Builder $query): Builder
    {
        return $query->withReviewAvgValue()->orderBy('reviews_avg_value');
    }

    /**
     * Scope a query to order by the average review value in descending order.
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeOrderByReviewValueDesc(Builder $query): Builder
    {
        return $query->withReviewAvgValue()->orderByDesc('reviews_avg_value');
    }

    /**
     * Scope a query to include the average value of a specific extra data key from associated reviews.
     *
     * @param Builder $query
     * @param string $key The specific extra data key for which the average value is calculated.
     *
     * @return Builder
     */
    public function scopeWithReviewAvgExtra(Builder $query, string $key): Builder
    {
        return $query->withAvg('reviews as reviews_avg_extra_' . $key, 'extra->' . $key);
    }

    /**
     * Scope a query to order the results by the average value of a specific extra data key from associated reviews in ascending order.
     *
     * @param Builder $query
     * @param string $key The specific extra data key used for ordering the results.
     *
     * @return Builder
     */
    public function scopeOrderByReviewExtra(Builder $query, string $key): Builder
    {
        return $query->withReviewAvgExtra($key)->orderBy('reviews_avg_extra_' . $key);
    }

    /**
     * Scope a query to order the results by the average value of a specific extra data key from associated reviews in descending order.
     *
     * @param Builder $query
     * @param string $key The specific extra data key used for ordering the results.
     *
     * @return Builder
     */
    public function scopeOrderByReviewExtraDesc(Builder $query, string $key): Builder
    {
        return $query->withReviewAvgExtra($key)->orderByDesc('reviews_avg_extra_' . $key);
    }
}
