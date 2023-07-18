<?php

namespace Larastash\Reviews\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'value',
        'title',
        'body',
        'extra',
    ];

    protected $casts = [
        'extra' => 'array',
    ];

    /**
     * Constructor.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('reviews.table'));
    }

    /**
     * Get a reviewable model
     *
     * @return MorphTo
     */
    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get a author of review.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to filter reviews by the given reviewable type.
     *
     * @param Builder $query The query builder instance.
     * @param string $type The fully qualified class name or an instance of the reviewable entity.
     *
     * @return Builder
     */
    public function scopeWithType(Builder $query, string $type): Builder
    {
        return $query->where('reviewable_type', app($type)->getMorphClass());
    }
}
