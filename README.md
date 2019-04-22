<h1 align="center">Laravel 5 Follow System</h1>

<p align="center">:heart: This package helps you to add user based follow system to your model.</p>

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

## Features

- Support actions:
    - Follow
    - Like
    - Bookmark
    - Subscribe
    - Favorite
    - Vote (Upvote & Downvote)

## Installation

### Required

- PHP 7.0 +
- Laravel 5.5 +

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

As optional if you want to modify the default configuration, you can publish the configuration file:
 
```sh
$ php artisan vendor:publish --provider="Overtrue\LaravelFollow\FollowServiceProvider" --tag="config"
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
use Overtrue\LaravelFollow\Traits\CanVote;
use Overtrue\LaravelFollow\Traits\CanBookmark;

class User extends Model
{
    use CanFollow, CanBookmark, CanLike, CanFavorite, CanSubscribe, CanVote;
}
```

Add `CanBeXXX` Trait to target model, such as 'Post' or 'Music' ...:

```php
use Overtrue\LaravelFollow\Traits\CanBeLiked;
use Overtrue\LaravelFollow\Traits\CanBeFavorited;
use Overtrue\LaravelFollow\Traits\CanBeVoted;
use Overtrue\LaravelFollow\Traits\CanBeBookmarked;

class Post extends Model
{
    use CanBeLiked, CanBeFavorited, CanBeVoted, CanBeBookmarked;
}
```

All available APIs are listed below.

### Follow

#### `\Overtrue\LaravelFollow\Traits\CanFollow`

```php
$user->follow($targets)
$user->unfollow($targets)
$user->toggleFollow($targets)
$user->followings()->get() // App\User:class
$user->followings(App\Post::class)->get()
$user->areFollowingEachOther($anotherUser);
$user->isFollowing($target)
```

#### `\Overtrue\LaravelFollow\Traits\CanBeFollowed`

```php
$object->followers()->get()
$object->isFollowedBy($user)
```

### Bookmark

#### `\Overtrue\LaravelFollow\Traits\CanBookmark`

```php
$user->bookmark($targets)
$user->unbookmark($targets)
$user->toggleBookmark($targets)
$user->hasBookmarked($target)
$user->bookmarks()->get() // App\User:class
$user->bookmarks(App\Post::class)->get()
```

#### `\Overtrue\LaravelFollow\Traits\CanBeBookmarked`

```php
$object->bookmarkers()->get() // or $object->bookmarkers 
$object->isBookmarkedBy($user)
```

### Like

#### `\Overtrue\LaravelFollow\Traits\CanLike`

```php
$user->like($targets)
$user->unlike($targets)
$user->toggleLike($targets)
$user->hasLiked($target)
$user->likes()->get() // default object: App\User:class
$user->likes(App\Post::class)->get()
```

#### `\Overtrue\LaravelFollow\Traits\CanBeLiked`

```php
$object->likers()->get() // or $object->likers
$object->fans()->get() // or $object->fans
$object->isLikedBy($user)
```

### Favorite

#### `\Overtrue\LaravelFollow\Traits\CanFavorite`

```php
$user->favorite($targets)
$user->unfavorite($targets)
$user->toggleFavorite($targets)
$user->hasFavorited($target)
$user->favorites()->get() // App\User:class
$user->favorites(App\Post::class)->get()
```

#### `\Overtrue\LaravelFollow\Traits\CanBeFavorited`

```php
$object->favoriters()->get() // or $object->favoriters 
$object->isFavoritedBy($user)
```

### Subscribe

#### `\Overtrue\LaravelFollow\Traits\CanSubscribe`

```php
$user->subscribe($targets)
$user->unsubscribe($targets)
$user->toggleSubscribe($targets)
$user->hasSubscribed($target)
$user->subscriptions()->get() // default object: App\User:class
$user->subscriptions(App\Post::class)->get()
```

#### `Overtrue\LaravelFollow\Traits\CanBeSubscribed`

```php
$object->subscribers() // or $object->subscribers 
$object->isSubscribedBy($user)
```

### Vote

#### `\Overtrue\LaravelFollow\Traits\CanVote`

```php
$user->vote($target) // Vote with 'upvote' for default
$user->upvote($target)
$user->downvote($target)
$user->cancelVote($target)
$user->hasUpvoted($target)
$user->hasDownvoted($target)
$user->votes(App\Post::class)->get()
$user->upvotes(App\Post::class)->get()
$user->downvotes(App\Post::class)->get()
```

#### `\Overtrue\LaravelFollow\Traits\CanBeVoted`

```php
$object->voters()->get()
$object->upvoters()->get()
$object->downvoters()->get()
$object->isVotedBy($user)
$object->isUpvotedBy($user)
$object->isDownvotedBy($user)
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
$user->follow($posts); // targets: [1, 2, ...], $class = App\Post
```

### Query relations

```php
$followers = $user->followers
$followers = $user->followers()->where('id', '>', 10)->get()
$followers = $user->followers()->orderByDesc('id')->get()
```

The other is the same usage.

### Working with model.

```php
use Overtrue\LaravelFollow\FollowRelation;

// get most popular object

// all types
$relations = FollowRelation::popular()->get();

// followable_type = App\Post
$relations = FollowRelation::popular(App\Post::class)->get(); 

// followable_type = App\User
$relations = FollowRelation::popular('user')->get();
 
// followable_type = App\Post
$relations = FollowRelation::popular('post')->get();

// Pagination
$relations = FollowRelation::popular(App\Post::class)->paginate(15); 

```

### Events

 - `Overtrue\LaravelFollow\RelationAttaching`
 - `Overtrue\LaravelFollow\RelationAttached`
 - `Overtrue\LaravelFollow\RelationDetaching`
 - `Overtrue\LaravelFollow\RelationDetached`
 - `Overtrue\LaravelFollow\RelationToggling`
 - `Overtrue\LaravelFollow\RelationToggled`


```php
Event::listen(\Overtrue\LaravelFollow\RelationAttached::class, function($event) {
    // $event->causer; 
    // $event->getTargetsCollection(); 
    // $event->getRelationType();
});
```

# About toggled event.

There has a extra properties for `Overtrue\LaravelFollow\RelationToggled` event.

```php
$event->results; // ['attached' => [1, 2, 3], 'detached' => [5, 6]]
$event->attached; // [1, 2, 3]
$event->detached; // [5, 6]
```

## PHP 扩展包开发

> 想知道如何从零开始构建 PHP 扩展包？
>
> 请关注我的实战课程，我会在此课程中分享一些扩展开发经验 —— [《PHP 扩展包实战教程 - 从入门到发布》](https://learnku.com/courses/creating-package)

## License

MIT
