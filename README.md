# Laravel 5 Follow System

:heart: This package helps you to add user based follow system to your model.

> this repo is forked from mohd-isa/follow(deleted).

## Features

- Support actions:
    - Follow
    - Like
    - Subscribe
    - Favorite

## Installation

You can install the package using composer

```sh
$ composer require overtrue/laravel-follow -vvv
```

Then add the service provider to `config/app.php`

```php
Overtrue\LaravelFollow\FollowServiceProvider::class
```

Publish the migrations file:

```sh
$ php artisan vendor:publish --provider='Overtrue\LaravelFollow\FollowServiceProvider' --tag="migrations"
```

As optional if you want to modify the default configuration, you can publish the configuration file:
 
```sh
$ php artisan vendor:publish --provider='Overtrue\LaravelFollow\FollowServiceProvider' --tag="config"
```

And create tables:

```php
$ php artisan migrate
```

Finally, add feature trait into User model:

```php
use Overtrue\LaravelFollow\Traits\CanFollow;
use Overtrue\LaravelFollow\Traits\CanBeFollowed;

class User extends Model
{
    use CanFollow, CanBeFollowed;
}
```

## Usage

Add `CanXXX` Traits to User model.

```php
use Overtrue\LaravelFollow\Traits\CanFollow;
use Overtrue\LaravelFollow\Traits\CanLike;
use Overtrue\LaravelFollow\Traits\CanFavorite;
use Overtrue\LaravelFollow\Traits\CanSubscribe;

class User extends Model
{
    use CanFollow, CanLike, CanFavorite, CanSubscribe;
}
```

Add `CanBeXXX` Trait to target model, such as 'Post' or 'Music' ...:

```php
use Overtrue\LaravelFollow\Traits\CanBeLiked;
use Overtrue\LaravelFollow\Traits\CanBeFavorited;

class Post extends Model
{
    use CanBeLiked, CanBeFavorited;
}
```

All available APIs are listed below.

### Follow

#### `\Overtrue\LaravelFollow\CanFollow`

```php
$user->follow(1)
$user->follow([1,2,3,4])
$user->unfollow(1)
$user->unfollow([1,2,3,4])
$user->followings()
$user->isFollowing(1)
```

#### `\Overtrue\LaravelFollow\CanBeFollowed`

```php
$object->followers()
$object->isFollowedBy(1)
```

### Like

#### `\Overtrue\LaravelFollow\CanLike`

```php
$user->like(1)
$user->like([1,2,3,4])
$user->unlike(1)
$user->unlike([1,2,3,4])
$user->hasLiked(1)
$user->likes()
```

#### `\Overtrue\LaravelFollow\CanBeLiked`

```php
$object->likers()
$object->fans()
$object->isLikedBy(1)
```

### Favorite

#### `\Overtrue\LaravelFollow\CanFavorite`

```php
$user->favorite(1)
$user->favorite([1,2,3,4])
$user->unfavorite(1)
$user->unfavorite([1,2,3,4])
$user->hasFavorited(1)
$user->favorites()
```

#### `\Overtrue\LaravelFollow\CanBeFavorited`

```php
$object->favoriters()
$object->isFavoritedBy(1)
```

### Subscribe

#### `\Overtrue\LaravelFollow\CanSubscribe`

```php
$user->subscribe(1)
$user->subscribe([1,2,3,4])
$user->unsubscribe(1)
$user->unsubscribe([1,2,3,4])
$user->hasSubscribed(1)
$user->subscriptions()
```

#### `Overtrue\LaravelFollow\CanBeSubscribed`

```php
$object->subscribers()
$object->isSubscribedBy(1)
```

## License

MIT
