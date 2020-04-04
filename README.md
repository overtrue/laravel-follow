<h1 align="center">Laravel Follow</h1>

<p align="center">User follow unfollow system for Laravel.</p>


## Installing

```shell
$ composer require overtrue/laravel-follow -vvv
```

### Migrations

This step is also optional, if you want to custom the pivot table, you can publish the migration files:

```php
$ php artisan vendor:publish --provider="Overtrue\\LaravelFollow\\FollowServiceProvider" --tag=migrations
```

## Usage

### Traits

#### `Overtrue\LaravelFollow\Followable`

```php

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Overtrue\LaravelFollow\Followable;

class User extends Authenticatable
{
    <...>
    use Followable;
    <...>
}
```

### API

```php
$user1 = User::find(1);
$user2 = User::find(2);

$user1->follow($user2);
$user1->unfollow($user2);
$user1->toggleFollow($user2);

$user1->isFollowing($user2); 
$user2->isFollowdBy($user1); 

$user1->areFollowingEachOther($user2);
```

#### Get followings:

```php
$user->followings; 
```

#### Get followers:

```php
$user->followers; 
```

### Aggregations

```php
// followings count
$user->followings()->count(); 

// with query where
$user->followings()->where('gender', 'female')->count(); 

// followers count
$post->followers()->count();
```

List with `*_count` attribute:

```php
$users = User::withCount(['followings', 'followers'])->get();

foreach($users as $user) {
    // $user->followings_count;
    // $user->followers_count;
}
```

### N+1 issue

To avoid the N+1 issue, you can use eager loading to reduce this operation to just 2 queries. When querying, you may specify which relationships should be eager loaded using the `with` method:

```php
$users = User::with('followings')->get();

foreach($users as $user) {
    $user->isFollowing(2);
}

$users = User::with('followers')->get();

foreach($users as $user) {
    $user->isFollowedBy(2);
}
```


### Events

| **Event** | **Description** |
| --- | --- |
|  `Overtrue\LaravelFollow\Events\Followd` | Triggered when the relationship is created. |
|  `Overtrue\LaravelFollow\Events\Unfollowd` | Triggered when the relationship is deleted. |

## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/overtrue/laravel-follow/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/overtrue/laravel-follow/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## PHP 扩展包开发

> 想知道如何从零开始构建 PHP 扩展包？
>
> 请关注我的实战课程，我会在此课程中分享一些扩展开发经验 —— [《PHP 扩展包实战教程 - 从入门到发布》](https://learnku.com/courses/creating-package)

## License

MIT
