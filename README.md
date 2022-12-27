<h1 align="center">Laravel Follow</h1>

<p align="center">User follow unfollow system for Laravel.</p>

<p align="center">
<a href="https://packagist.org/packages/overtrue/laravel-follow"><img src="https://poser.pugx.org/overtrue/laravel-follow/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/overtrue/laravel-follow"><img src="https://poser.pugx.org/overtrue/laravel-follow/v/unstable.svg" alt="Latest Unstable Version"></a>
<a href="https://scrutinizer-ci.com/g/overtrue/laravel-follow/build-status/master"><img src="https://scrutinizer-ci.com/g/overtrue/laravel-follow/badges/build.png?b=master" alt="Build Status"></a>
<a href="https://scrutinizer-ci.com/g/overtrue/laravel-follow/?branch=master"><img src="https://scrutinizer-ci.com/g/overtrue/laravel-follow/badges/quality-score.png?b=master" alt="Scrutinizer Code Quality"></a>
<a href="https://packagist.org/packages/overtrue/laravel-follow"><img src="https://poser.pugx.org/overtrue/laravel-follow/downloads" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/overtrue/laravel-follow"><img src="https://poser.pugx.org/overtrue/laravel-follow/license" alt="License"></a>
</p>

Related projects:

- Like: [overtrue/laravel-like](https://github.com/overtrue/laravel-like)
- Favorite: [overtrue/laravel-favorite](https://github.com/overtrue/laravel-favorite)
- Subscribe: [overtrue/laravel-subscribe](https://github.com/overtrue/laravel-subscribe)
- Vote: [overtrue/laravel-vote](https://github.com/overtrue/laravel-vote)

[![Sponsor me](https://github.com/overtrue/overtrue/blob/master/sponsor-me-button-s.svg?raw=true)](https://github.com/sponsors/overtrue)

## Installing

```shell
composer require overtrue/laravel-follow -vvv
```

### Configuration and Migrations

```shell
php artisan vendor:publish
```

## Usage

### Traits

#### `Overtrue\LaravelFollow\Traits\Follower`

Add the Follower trait to your user model:

```php

use Overtrue\LaravelFollow\Traits\Follower;

class User extends Authenticatable
{
    use Follower;

    <...>
}
```

#### `Overtrue\LaravelFollow\Traits\Followable`

Then add the Followable trait to your followable model, for example `App\User`:

```php
use Overtrue\LaravelFollow\Traits\Followable;

class User extends Authenticatable
{
    use Followable;
    <...>
}
```

or any other model:

```php
use Overtrue\LaravelFollow\Traits\Followable;

class Channel extends Model
{
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
$user2->isFollowedBy($user1);
$user2->hasRequestedToFollow($user1);
```

#### Get followings:

```php
$user->followings;
$user->approvedFollowings;
$user->notApprovedFollowings;
```

```php
foreach($user->followings()->with('followable')->get() as $following)
{
    $following->created_at; // followed at
    
    $following->followable->nickname; // the user attributes
}
```


#### Get followers:

```php
$user->followers;
$user->approvedFollowers;
$user->notApprovedFollowers;
```

```php
foreach($user->followers()->with('follower')->get() as $follower)
{
    $follower->created_at; // followed at
    
    $follower->follower->nickname; // the user attributes
}
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
$user->approvedFollowings()->count();
$user->notApprovedFollowings()->count();

// with query where
$user->followings()->where('gender', 'female')->count();

// followers count
$user->followers()->count();
$user->approvedFollowers()->count();
$user->notApprovedFollowers()->count();
```

List with `*_count` attribute:

```php
$users = User::withCount(['followings', 'followables'])->get();
// or 
$users = User::withCount(['approvedFollowings', 'approvedFollowers'])->get();

foreach($users as $user) {
    // $user->followings_count;
    // $user->followers_count;
    // or 
    // $user->approved_followings_count;
    // $user->approved_followers_count;
}
```

### Attach user follow status to followable collection

You can use `Follower::attachFollowStatus(Collection $followables)` to attach the user follow status, it will set `has_followed` attribute to each model of `$followables`:

#### For model

```php
$user1 = User::find(1);

$user->attachFollowStatus($user1);

// result
[
    "id" => 1
    "name" => "user1"
    "private" => false
    "created_at" => "2021-06-07T15:06:47.000000Z"
    "updated_at" => "2021-06-07T15:06:47.000000Z"
    "has_followed" => true  
  ]
```

#### For `Collection | Paginator | LengthAwarePaginator | array`:

```php
$user = auth()->user();

$users = User::oldest('id')->get();

$users = $user->attachFollowStatus($users);

$users = $users->toArray();

// result
[
  [
    "id" => 1
    "name" => "user1"
    "private" => false
    "created_at" => "2021-06-07T15:06:47.000000Z"
    "updated_at" => "2021-06-07T15:06:47.000000Z"
    "has_followed" => true  
  ],
  [
    "id" => 2
    "name" => "user2"
    "private" => false
    "created_at" => "2021-06-07T15:06:47.000000Z"
    "updated_at" => "2021-06-07T15:06:47.000000Z"
    "has_followed" => true
  ],
  [
    "id" => 3
    "name" => "user3"
    "private" => false
    "created_at" => "2021-06-07T15:06:47.000000Z"
    "updated_at" => "2021-06-07T15:06:47.000000Z"
    "has_followed" => false
  ],
  [
    "id" => 4
    "name" => "user4"
    "private" => false
    "created_at" => "2021-06-07T15:06:47.000000Z"
    "updated_at" => "2021-06-07T15:06:47.000000Z"
    "has_followed" => false
  ],
]
```

#### For pagination

```php
$users = User::paginate(20);

$user->attachFollowStatus($users);
```


### Order by followers count

You can query users order by followers count with following methods:

- `orderByFollowersCountDesc()`
- `orderByFollowersCountAsc()`
- `orderByFollowersCount(string $direction = 'desc')`

example:

```php
$users = User::orderByFollowersCountDesc()->get();
$mostPopularUser = User::orderByFollowersCountDesc()->first();
```

### N+1 issue

To avoid the N+1 issue, you can use eager loading to reduce this operation to just 2 queries. When querying, you may specify which relationships should be eager loaded using the `with` method:

```php
$users = User::with('followings')->get();

foreach($users as $user) {
    $user->isFollowing(2);
}

$users = User::with('followables')->get();

foreach($users as $user) {
    $user->isFollowedBy(2);
}
```

### Events

| **Event**                                 | **Description**                             |
| ----------------------------------------- | ------------------------------------------- |
| `Overtrue\LaravelFollow\Events\Followed`   | Triggered when the relationship is created. |
| `Overtrue\LaravelFollow\Events\Unfollowed` | Triggered when the relationship is deleted. |

## :heart: Sponsor me 

[![Sponsor me](https://github.com/overtrue/overtrue/blob/master/sponsor-me.svg?raw=true)](https://github.com/sponsors/overtrue)

如果你喜欢我的项目并想支持它，[点击这里 :heart:](https://github.com/sponsors/overtrue)

## Project supported by JetBrains

Many thanks to Jetbrains for kindly providing a license for me to work on this and other open-source projects.

[![](https://resources.jetbrains.com/storage/products/company/brand/logos/jb_beam.svg)](https://www.jetbrains.com/?from=https://github.com/overtrue)


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
