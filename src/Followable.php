<?php

/*
 * This file is part of the overtrue/laravel-follow
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\LaravelFollow;

use Illuminate\Database\Eloquent\Model;

/**
 * Trait Followable.
 *
 * @property \Illuminate\Database\Eloquent\Collection $followings
 * @property \Illuminate\Database\Eloquent\Collection $followers
 */
trait Followable
{
    /**
     * @param \Illuminate\Database\Eloquent\Model|int $user
     */
    public function follow($user)
    {
        $this->followings()->attach($user);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model|int $user
     *
     * @return int
     */
    public function unfollow($user)
    {
        return $this->followings()->detach($user);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model|int $user
     *
     * @return array|array[]
     */
    public function toggleFollow($user)
    {
        return $this->followings()->toggle($user);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model|int $user
     *
     * @return bool
     */
    public function isFollowing($user)
    {
        if ($user instanceof Model) {
            $user = $user->getKey();
        }

        /* @var \Illuminate\Database\Eloquent\Model $this */
        if ($this->relationLoaded('followings')) {
            return $this->followings->contains($user);
        }

        return $this->followings()->where($this->getQualifiedKeyName(), $user)->exists();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model|int $user
     *
     * @return bool
     */
    public function isFollowedBy($user)
    {
        if ($user instanceof Model) {
            $user = $user->getKey();
        }

        /* @var \Illuminate\Database\Eloquent\Model $this */
        if ($this->relationLoaded('followers')) {
            return $this->followers->contains($user);
        }

        return $this->followers()->where($this->getQualifiedKeyName(), $user)->exists();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model|int $user
     *
     * @return bool
     */
    public function areFollowingEachOther($user)
    {
        /* @var \Illuminate\Database\Eloquent\Model $user*/
        return $this->isFollowing($user) && $this->isFollowedBy($user);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followers()
    {
        /* @var \Illuminate\Database\Eloquent\Model $this */
        return $this->belongsToMany(
            __CLASS__,
            \config('follow.relation_table', 'user_follower'),
            'following_id',
            'follower_id'
        )->withTimestamps()->using(UserFollower::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followings()
    {
        /* @var \Illuminate\Database\Eloquent\Model $this */
        return $this->belongsToMany(
            __CLASS__,
            \config('follow.relation_table', 'user_follower'),
            'follower_id',
            'following_id'
        )->withTimestamps()->using(UserFollower::class);
    }
}
