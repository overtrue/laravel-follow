<h1 align="center">Laravel Follow</h1>

<p align="center">User follow unfollow system for Laravel.</p>

<p align="center">
<a href="https://travis-ci.org/overtrue/laravel-follow"><img src="https://travis-ci.org/overtrue/laravel-follow.svg?branch=master" alt="Build Status"></a>
<a href="https://packagist.org/packages/overtrue/laravel-follow"><img src="https://poser.pugx.org/overtrue/laravel-follow/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/overtrue/laravel-follow"><img src="https://poser.pugx.org/overtrue/laravel-follow/v/unstable.svg" alt="Latest Unstable Version"></a>
<a href="https://scrutinizer-ci.com/g/overtrue/laravel-follow/build-status/master"><img src="https://scrutinizer-ci.com/g/overtrue/laravel-follow/badges/build.png?b=master" alt="Build Status"></a>
<a href="https://scrutinizer-ci.com/g/overtrue/laravel-follow/?branch=master"><img src="https://scrutinizer-ci.com/g/overtrue/laravel-follow/badges/quality-score.png?b=master" alt="Scrutinizer Code Quality"></a>
<a href="https://scrutinizer-ci.com/g/overtrue/laravel-follow/?branch=master"><img src="https://scrutinizer-ci.com/g/overtrue/laravel-follow/badges/coverage.png?b=master" alt="Code Coverage"></a>
<a href="https://packagist.org/packages/overtrue/laravel-follow"><img src="https://poser.pugx.org/overtrue/laravel-follow/downloads" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/overtrue/laravel-follow"><img src="https://poser.pugx.org/overtrue/laravel-follow/license" alt="License"></a>
</p>

> 💡 The new version has been split into several packages:
>
> - Follow: [overtrue/laravel-follow](https://github.com/overtrue/laravel-follow)
> - Like: [overtrue/laravel-like](https://github.com/overtrue/laravel-like)
> - Favorite: [overtrue/laravel-favorite](https://github.com/overtrue/laravel-favorite)
> - Subscribe: [overtrue/laravel-subscribe](https://github.com/overtrue/laravel-subscribe)
> - Vote: overtrue/laravel-vote (working in progress)
> - Bookmark: overtrue/laravel-bookmark (working in progress)

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
$user1->acceptFollowRequestFrom($user2);
$user1->rejectFollowRequestFrom($user2);

$user1->isFollowing($user2);
$user2->isFollowdBy($user1);
$user2->hasRequestedToFollow($user1);

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

### Follow Requests

If you would like to have some follow requests to need to be accepted by the user being followed, simply override the **needsToApproveFollowRequests()** method in the model that uses the **Followable** trait with your custom logic:

```php
public function needsToApproveFollowRequests()
{
    // Your custom logic here
    return (bool) $this->private;
}
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

| **Event**                                 | **Description**                             |
| ----------------------------------------- | ------------------------------------------- |
| `Overtrue\LaravelFollow\Events\Followd`   | Triggered when the relationship is created. |
| `Overtrue\LaravelFollow\Events\Unfollowd` | Triggered when the relationship is deleted. |

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
