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
 * Trait CanLike.
 */
trait CanLike
{
    /**
     * Like an item or items.
     *
     * @param int|array|\Illuminate\Database\Eloquent\Model $targets
     * @param string                                        $class
     *
     * @throws \Exception
     *
     * @return array
     */
    public function like($targets, $class = __CLASS__)
    {
        return Follow::attachRelations($this, 'likes', $targets, $class);
    }

    /**
     * Unlike an item or items.
     *
     * @param int|array|\Illuminate\Database\Eloquent\Model $targets
     * @param string                                        $class
     *
     * @return array
     */
    public function unlike($targets, $class = __CLASS__)
    {
        return Follow::detachRelations($this, 'likes', $targets, $class);
    }

    /**
     * Toggle like an item or items.
     *
     * @param int|array|\Illuminate\Database\Eloquent\Model $targets
     * @param string                                        $class
     *
     * @throws \Exception
     *
     * @return array
     */
    public function toggleLike($targets, $class = __CLASS__)
    {
        return Follow::toggleRelations($this, 'likes', $targets, $class);
    }

    /**
     * Check if user is liked given item.
     *
     * @param int|array|\Illuminate\Database\Eloquent\Model $target
     * @param string                                        $class
     *
     * @return bool
     */
    public function hasLiked($target, $class = __CLASS__)
    {
        return Follow::isRelationExists($this, 'likes', $target, $class);
    }

    /**
     * Return item likes.
     *
     * @param string $class
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function likes($class = __CLASS__)
    {
        return $this->morphedByMany($class, config('follow.morph_prefix'), config('follow.followable_table'))
                    ->wherePivot('relation', '=', Follow::RELATION_LIKE)
                    ->withPivot('followable_type', 'relation', 'created_at');
    }
}
