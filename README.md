# Laravel Reviews

The Larastash Reviews package is a powerful Laravel package that enables you to add review functionalities to your Eloquent models.

With this package, you can easily manage reviews for various reviewable entities and perform various review-related operations.

## Requirements

- Laravel 10;
- PHP ^8.1;

## Installation

To install the Larastash Reviews package, you can use Composer:

```shell
composer require larastash/reviews
```

After installing the package, publish migration and config files:

```shell
php artisan vendor:publish --tag="larastash:reviews"
```

> **Note**
>
> You can edit migration and set `foreignUuid` if your user model uses a UUID.

## Prepare Models

To use the Larastash Reviews package, you need to apply the [`Reviewable`](src/Concerns/Reviewable.php) trait to the Eloquent model that you want to make reviewable.

```php
namespace App\Models;

...
use Larastash\Reviews\Concerns\Reviewable;

class Product extends Model
{
    use Reviewable;

    ...
}
```

Additionally, you can apply the [`Reviewer`](src/Concerns/Reviewer.php) trait to the User model.

```php
namespace App\Models;

...
use Larastash\Reviews\Concerns\Reviewer;

class User extends Model
{
    use Reviewer;

    ...
}
```

## Usage

The following examples demonstrate the usage of the Laravel package for managing reviews.

```php
review($product)
$product->review();
```

- `review($product)`: Creates a new `Larastash\Reviews\Review` instance for the given `$product`. It returns a `Larastash\Reviews\Review` instance.
- `$product->review()`: An alternative way to create a new `Larastash\Reviews\Review` instance for the given `$product`. It also returns a `Larastash\Reviews\Review` instance.

Creates or updates a review for the `$product` entity with the provided values. The parameters `$value`, `$body`, `$title`, `$extra`, and `$userId` are used to set the properties of the review.

```php
$product->review($value, $body, $title, $extra, $userId);
```

### Publish Reviews

To create a new review (uses `updateOrCreate` under the hood), use the `publish` method.

```php
review($product)->publish(5); // only value
review($product)->publish(5, 'I love it!'); // value & body
review($product)->publish(5, 'I love it!', 'Awesome'); // value, body & title
review($product)->publish(5, title: 'Awesome'); // value & title
```

### With Extra Data

You can pass additional data to the review, such as approved, anonymous review, recommended, etc.

```php
review($product)->extra(['approved' => false, 'recommended' => 1])->publish(5);
```

```php
review($product)->with('approved', false)->with('recommended', 1)->publish(5);
```

> **Note**
>
> You can also work with this data, for example, choose an average extra value or get only approved reviews.
>
### Another User (Reviewer)

Publish review as another user.

```php
review($product)->as(User::find(1337))->publish(5);
review($product)->as(1337)->publish(5);
```

> **Note**
>
> By default, the overview is owned by the current authorized user (by `Auth::id()`).

### Update Review

Sometimes, when we have some extra data, for example, we need to change only `approved`, then we can use the `update` method.

```php
review($product)->with('approved', true)->update(5);
```

```php
review($product)->by(User::find(1337))->with('approved', true)->update(5);
review($product)->by(1337)->with('approved', true)->update(5);
```

It will change the value `approved` to `true`, and will not affect other extra data, such as `recommended`.

Of course, you can use the `publish` method to update the review. But then you will need to pass the full current extra data, not just the `approved = true`.

### User Has Review

Check if the user has a review or not.

```php
review($product)->exists();
```

```php
review($product)->by(User::find(1337))->exists();
review($product)->by(1337)->exists();
```

### Delete Review

Deletes the user's review.

```php
review($product)->delete();
```

```php
review($product)->by(User::find(1337))->delete();
review($product)->by(1337)->delete();
```

### Total Number of Reviews

Get the total number of reviews for a entry.

```php
review($product)->total();
```

### Avg Value and Extras

Get the average value of a review.

```php
review($product)->avg();
review($product)->avg(precision: 0); // 2 by default
```

Get the average extra value of a review.

```php
review($product)->avg('recommended');
review($product)->avg('recommended', 0); // precision is 2 by default
```

### Reviewable Query Builder

Get the review query builder instance.

```php
review($product)->query()->doSomething();
```

### Eager Loading

```php
$product = Product::with('reviews');
```

### Related Reviewable Methods

```php
$product = Product::withReviewAvgValue();
$product->reviews_avg_value;
```

```php
Product::orderByReviewValue();
```

```php
Product::orderByReviewValueDesc();
```

```php
$product = Product::withReviewAvgExtra('recommended')->first();
$product->reviews_avg_extra_recommended;
```

```php
Product::orderByReviewExtra('recommended');
```

```php
Product::orderByReviewExtraDesc('recommended');
```

```php
use App\Models\Product;
use Larastash\Reviews\Models\Review;

Review::withType(Product::class)->count();
```

### Get User Reviews

This will be available if you add the `Larastash\Reviews\Concerns\Reviewer` trait to the `User` model.

```php
auth()->user()->reviews;
```

## Helpers

### `review()`

This function, `review`, is a helper function provided by the Laravel package.

It creates a new `Larastash\Reviews\Review` instance for the given reviewable entity (a model that uses the `Reviewable` trait). This function is particularly useful when you want to interact with the review-related methods of the `Review` class for a specific model instance. It saves you from manually creating a new `Review` instance each time you want to perform actions related to reviews for a specific entity.

## Testing

``` bash
$ composer test
```

## Contributing
If you find any issues or have suggestions for improvement, please feel free to contribute by creating a pull request or submitting an issue.

## Credits

- [chipslays](https://github.com/chipslays)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.