<?php

namespace Larastash\Reviews;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;
use Larastash\Reviews\Models\Review as ReviewModel;
use Exception;

class Review
{
    /**
     * The data array to store review-related information.
     *
     * @var array
     */
    protected array $data = [
        'extra' => [],
    ];

    /**
     * Create a new Review instance.
     *
     * @param Model $reviewable The reviewable entity for which the review is being created.
     *
     * @throws Exception If the provided model is not reviewable.
     */
    public function __construct(protected Model $reviewable)
    {
        if (!method_exists($reviewable, 'reviews')) {
            throw new Exception(sprintf(
                'Model `%s` is no reviewable. Are you sure you added the `Reviewable` trait to the model?',
                get_class($reviewable)
            ));
        }

        $this->data['user_id'] = Auth::id();
    }

    /**
     * Specify the user who is creating the review.
     *
     * @param Model|int $user The user model or the user ID who is creating the review.
     *
     * @return static
     */
    public function as(Model|int $user): static
    {
        $this->data['user_id'] = $user instanceof Model ? $user->id : $user;

        return $this;
    }

    /**
     * Alias for `as`.
     *
     * @param Model|int $user
     * @return static
     */
    public function by(Model|int $user): static
    {
        return $this->as($user);
    }

    /**
     * Add extra data to the review.
     *
     * @param array $extra Extra data associated with the review.
     *
     * @return static
     */
    public function extra(array $extra): static
    {
        $this->data['extra'] = $extra;

        return $this;
    }

    /**
     * Add a specific key-value pair to the extra data of the review.
     *
     * @param string $key The key of the extra data.
     * @param mixed $value The value of the extra data.
     *
     * @return static
     */
    public function with(string $key, mixed $value): static
    {
        $this->data['extra'][$key] = $value;

        return $this;
    }

    /**
     * Create a new review.
     *
     * This method used `updateOrCreate` under the hood.
     *
     * @param int $value The numeric value of the review.
     * @param string|null $body  The body text of the review.
     * @param string|null $title The title of the review.
     *
     * @return \Larastash\Reviews\Models\Review
     */
    public function publish(int $value, string|null $body = null, string|null $title = null): ReviewModel
    {
        return $this->reviewable->review(
            $value,
            $body,
            $title,
            $this->data['extra'],
            $this->data['user_id'],
        );
    }

    /**
     * Update an existing review or create a new review if none exists for the specified reviewable entity and user.
     *
     * @param int $value The numeric value of the review.
     * @param string|null $body The body text of the review (optional).
     * @param string|null $title The title of the review (optional).
     *
     * @return \Larastash\Reviews\Models\Review The updated or newly created review instance.
     */
    public function update(int $value, string|null $body = null, string|null $title = null): ReviewModel
    {
        $review = ReviewModel::query()
            ->where('reviewable_id', $this->reviewable->id)
            ->where('reviewable_type', get_class($this->reviewable))
            ->where('user_id', $this->data['user_id'])
            ->first();

        if (!$review) {
            return $this->publish($value, $body, $title);
        }

        $review->forceFill([
            'value' => $value,
            'body' => $body,
            'title' => $title,
            'extra' => [...$review->extra, ...$this->data['extra']],
        ])->save();

        return $review;
    }

    /**
     * Check if a review exists for the specified reviewable entity and user.
     *
     * @return bool
     */
    public function exists(): bool
    {
        return ReviewModel::query()
            ->where('reviewable_id', $this->reviewable->id)
            ->where('reviewable_type', get_class($this->reviewable))
            ->where('user_id', $this->data['user_id'])
            ->exists();
    }

    /**
     * Delete the review for the specified reviewable entity and user.
     *
     * @return bool
     */
    public function delete(): bool
    {
        return (bool) ReviewModel::query()
            ->where('reviewable_id', $this->reviewable->id)
            ->where('reviewable_type', get_class($this->reviewable))
            ->where('user_id', $this->data['user_id'])
            ->delete();
    }

    /**
     * Calculate the average value of reviews.
     *
     * @param string|null $extra The specific extra data key for calculating the average.
     * @param int $precision The number of decimal places for the average value.
     *
     * @return int|float
     */
    public function avg(?string $extra = null, int $precision = 2): int|float
    {
        return round($this->query()->avg($extra ? 'extra->' . $extra : 'value'), $precision);
    }

    /**
     * Get the total count of reviews for the specified reviewable entity.
     *
     * @return int
     */
    public function total(): int
    {
        return $this->query()->withType(get_class($this->reviewable))->count();
    }

    /**
     * Get the review query builder instance.
     *
     * @return MorphMany
     */
    public function query(): MorphMany
    {
        return $this->reviewable->reviews();
    }
}
