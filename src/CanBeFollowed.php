<?php

/*
 * This file is part of the overtrue/laravel-follow.
 *
 * (c) Jiajian Chan <changejian@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\LaravelFollow;

/**
 * Trait CanBeFollowed.
 */
trait CanBeFollowed
{
    /**
     * Check if user is followed by given user.
     *
     * @param $user
     *
     * @return bool
     */
    public function isFollowedBy($user)
    {
        return $this->followers->contains($user);
    }

    /**
     * Return followers.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followers()
    {
        return $this->morphToMany($this->getProperty(), 'followable', 'followers');
    }

    /**
     * Return user following items.
     *
     * @return string
     */
    protected function getProperty()
    {
        return property_exists($this, 'follow') ? $this->follow : __CLASS__;
    }
}
