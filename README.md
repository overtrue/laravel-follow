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
$user->follow($targets)
$user->unfollow($targets)
$user->followings() // App\User:class
$user->followings(App\Post::class)
$user->isFollowing($target)
```

#### `\Overtrue\LaravelFollow\CanBeFollowed`

```php
$object->followers()
$object->isFollowedBy($user)
```

### Like

#### `\Overtrue\LaravelFollow\CanLike`

```php
$user->like($targets)
$user->unlike($targets)
$user->hasLiked($target)
$user->likes() // App\User:class
$user->likes(App\Post::class) 
```

#### `\Overtrue\LaravelFollow\CanBeLiked`

```php
$object->likers()
$object->fans() 
$object->isLikedBy($user)
```

### Favorite

#### `\Overtrue\LaravelFollow\CanFavorite`

```php
$user->favorite($targets)
$user->unfavorite($targets)
$user->hasFavorited($target)
$user->favorites() // App\User:class
$user->favorites(App\Post::class)
```

#### `\Overtrue\LaravelFollow\CanBeFavorited`

```php
$object->favoriters()
$object->isFavoritedBy($user)
```

### Subscribe

#### `\Overtrue\LaravelFollow\CanSubscribe`

```php
$user->subscribe($targets)
$user->unsubscribe($targets)
$user->hasSubscribed($target)
$user->subscriptions() // App\User:class
$user->subscriptions(App\Post::class)
```

#### `Overtrue\LaravelFollow\CanBeSubscribed`

```php
$object->subscribers()
$object->isSubscribedBy($user)
```

### Parameters

All of the above mentioned methods of creating relationships, such as 'follow', 'like', 'unfollow', 'unlike', their syntax is as follows:

```php
follow(array|int|\Illuminate\Database\Eloquent\Model $targets, $class = __CLASS__)
```

So you can call them like this:

```php
// Id / Id array
$user->follow(1); // targets: 1, $class = App\User
$user->follow(1, App\Post::class); // targets: 1, $class = App\Post
$user->follow([1, 2, 3]); // targets: [1, 2, 3], $class = App\User

// Model
$post = App\Post::find(7);
$user->follow($post); // targets: $post->id, $class = App\Post

// Model array
$posts = App\Post::popular()->get();
$user->follow($post); // targets: [$posts->id, ...], $class = App\Post
```

## License

MIT
