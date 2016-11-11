<?php

/*
 * This file is part of the overtrue/laravel-follow.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\LaravelFollow;

trait FollowTrait
{
    /**
     * Follow a user.
     *
     * @param int|array $user
     */
    public function follow($user)
    {
        if (!is_array($user)) {
            $user = compact('user');
        }

        $this->followings()->sync($user, false);
    }

    /**
     * Unfollow a user.
     *
     * @param $user
     */
    public function unfollow($user)
    {
        if (!is_array($user)) {
            $user = compact('user');
        }

        $this->followings()->detach($user);
    }

    /**
     * Check if user is following given user.
     *
     * @param $user
     *
     * @return mixed
     */
    public function isFollowing($user)
    {
        return $this->followings->contains($user);
    }

    /**
     * Check if user is followed by given user.
     *
     * @param $user
     *
     * @return mixed
     */
    public function isFollowedBy($user)
    {
        return $this->followers->contains($user);
    }

    /**
     * Followers relationship.
     *
     * @return mixed
     */
    public function followers()
    {
        return $this->belongsToMany(__CLASS__, 'followers', 'follow_id', 'user_id');
    }

    /**
     * Followings relationship.
     *
     * @return mixed
     */
    public function followings()
    {
        return $this->belongsToMany(__CLASS__, 'followers', 'user_id', 'follow_id');
    }
}
