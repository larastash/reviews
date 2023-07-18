<?php

namespace Larastash\Reviews\Providers;

use Illuminate\Support\ServiceProvider;

class ReviewsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/reviews.php' => config_path('reviews.php'),
            ], 'larastash:reviews');

            if (!class_exists('CreateReviewsTable')) {
                $this->publishes([
                    __DIR__ . '/../../database/migrations/2023_01_01_000001_create_reviews_table.php' => database_path('migrations/2023_01_01_000001_create_reviews_table.php'),
                ], 'larastash:reviews');
            }
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/reviews.php', 'reviews');
    }
}
