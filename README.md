# Laravel 5 Follow System

:heart: This package helps you to add user based follow system to your model.

> this repo is forked from mohd-isa/follow(deleted).

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
$ php artisan vendor:publish --provider="Overtrue\LaravelFollow\FollowServiceProvider" --tag="migrations"
```

Finally, use FollowTrait and FollowerTrait in User model

```php
use Overtrue\LaravelFollow\FollowTrait;
use Overtrue\LaravelFollow\FollowerTrait;

class User extends Model
{
    use FollowTrait, FollowerTrait;
}
```

## Usage

### Follow a user or users.

```php
$user->follow(1)
$user->follow([1,2,3,4])
```

### Unfollow a user or users.

```php
$user->unfollow(1)
$user->unfollow([1,2,3,4])
```

### Get user followers

```php
$user->followers()
```

### Get user followings

```php
$user->followings()
```

### Check if follow
```
$user->isFollowing(1)
```

### Check if followed by

```php
$user->isFollowedBy(1)
```

## License

MIT
