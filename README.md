# Laravel 5 Follow System

This package helps you to add user based follow system to your model.

## Installation

You can install the package using composer

```
$ composer require overtrue/follow -vvv
```

Then add the service provider to `config/app.php`

```
Overtrue\LaravelFollow\FollowServiceProvider::class
```

Publish the migrations file:

```
$ php artisan vendor:publish --provider="Overtrue\LaravelFollow\FollowServiceProvider" --tag="migrations"
```

Finally, use FollowTrait in User model

```
use Overtrue\LaravelFollow\FollowTrait;

class User extends Model
{
  use FollowTrait;
}
```

## Usage

### Follow User
```
$user->follow(1)
$user->follow([1,2,3,4])
```

### Unfollow User
```
$user->unfollow(1)
$user->unfollow([1,2,3,4])
```

### Get Followers
```
$user->followers()
```

### Get Followings
```
$user->followings()
```

### Check if Follow
```
$user->isFollowing(1)
```

### Check if Followed By
```
$user->isFollowedBy(1)
```

## Testing
You can run test by just performing `phpunit` command

## License

MIT
