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

/**
 * Trait FollowTrait.
 */
trait FollowTrait
{
    protected $class = __CLASS__;

    /**
     * Follow a item or items.
     *
     * @param int|array|\Illuminate\Database\Eloquent\Model $item
     *
     * @return int
     */
    public function follow($item)
    {
        $item = $this->checkItem($item);

        return $this->followings($this->class)->sync((array)$item, false);
    }

    /**
     * Unfollow a item or items.
     *
     * @param int|array|\Illuminate\Database\Eloquent\Model $item
     *
     * @return int
     */
    public function unfollow($item)
    {
        $item = $this->checkItem($item);

        return $this->followings($this->class)->detach((array)$item);
    }

    /**
     * Check if user is following given item.
     *
     * @param $item
     *
     * @return bool
     */
    public function isFollowing($item)
    {
        $item = $this->checkItem($item);

        return $this->followings($this->class)->get()->contains($item);
    }

    /**
     * Return item followings.
     * 
     * @param class $class
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followings($class = __CLASS__)
    {
        return $this->morphedByMany($class, 'followable', 'followers');
    }

    /**
     * Determine whether $item is an instantiated object of \Illuminate\Database\Eloquent\Model
     * 
     * @param $item
     * 
     * @return int
     */
    protected function checkItem($item)
    {
        if ($item instanceof \Illuminate\Database\Eloquent\Model) {
            $this->class = get_class($item);
            return $item->id;
        };

        return $item;
    }
}
