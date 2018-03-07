<?php

/*
 * This file is part of the overtrue/laravel-follow
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\LaravelFollow\Traits;

use Overtrue\LaravelFollow\Follow;

/**
 * Trait CanFollow.
 */
trait CanFollow
{
    /**
     * Follow an item or items.
     *
     * @param int|array|\Illuminate\Database\Eloquent\Model $targets
     * @param string                                        $class
     *
     * @return array
     *
     * @throws \Exception
     */
    public function follow($targets, $class = __CLASS__)
    {
        return Follow::attachRelations($this, 'followings', $targets, $class);
    }

    /**
     * Unfollow an item or items.
     *
     * @param int|array|\Illuminate\Database\Eloquent\Model $targets
     * @param string                                        $class
     *
     * @return array
     */
    public function unfollow($targets, $class = __CLASS__)
    {
        return Follow::detachRelations($this, 'followings', $targets, $class);
    }

    /**
     * Toggle follow an item or items.
     *
     * @param int|array|\Illuminate\Database\Eloquent\Model $targets
     * @param string                                        $class
     *
     * @return array
     *
     * @throws \Exception
     */
    public function toggleFollow($targets, $class = __CLASS__)
    {
        return Follow::toggleRelations($this, 'followings', $targets, $class);
    }

    /**
     * Check if user is following given item.
     *
     * @param int|array|\Illuminate\Database\Eloquent\Model $target
     * @param string                                        $class
     *
     * @return bool
     */
    public function isFollowing($target, $class = __CLASS__)
    {
        return Follow::isRelationExists($this, 'followings', $target, $class);
    }

    /**
     * Return item followings.
     *
     * @param string $class
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followings($class = __CLASS__)
    {
        return $this->morphedByMany($class, config('follow.morph_prefix'), config('follow.followable_table'))
                    ->wherePivot('relation', '=', Follow::RELATION_FOLLOW)
                    ->withPivot('followable_type', 'relation', 'created_at');
    }
}
