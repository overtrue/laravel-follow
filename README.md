# Laravel 5 Follow System

:two_men_holding_hands: This package helps you to add user based follow system to your model.

## Installation

You can install the package using composer

```sh
$ composer require overtrue/follow -vvv
```

Then add the service provider to `config/app.php`

```php
Overtrue\LaravelFollow\FollowServiceProvider::class
```

Publish the migrations file:

```sh
$ php artisan vendor:publish --provider="Overtrue\LaravelFollow\FollowServiceProvider" --tag="migrations"
```

Finally, use FollowTrait in User model

```php
use Overtrue\LaravelFollow\FollowTrait;

class User extends Model
{
  use FollowTrait;
}
```

## Usage

### Follow User

```php
$user->follow(1)
$user->follow([1,2,3,4])
```

### Unfollow User

```php
$user->unfollow(1)
$user->unfollow([1,2,3,4])
```

### Get Followers

```php
$user->followers()
```

### Get Followings

```php
$user->followings()
```

### Check if Follow
```
$user->isFollowing(1)
```

### Check if Followed By

```php
$user->isFollowedBy(1)
```

## License

MIT
