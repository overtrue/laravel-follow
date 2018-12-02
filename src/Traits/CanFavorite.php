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
 * Trait CanFavorite.
 */
trait CanFavorite
{
    /**
     * Favorite an item or items.
     *
     * @param int|array|\Illuminate\Database\Eloquent\Model $targets
     * @param string                                        $class
     *
     * @return array
     */
    public function favorite($targets, $class = __CLASS__)
    {
        return Follow::attachRelations($this, 'favorites', $targets, $class);
    }

    /**
     * Unfavorite an item or items.
     *
     * @param int|array|\Illuminate\Database\Eloquent\Model $targets
     * @param string                                        $class
     *
     * @return array
     */
    public function unfavorite($targets, $class = __CLASS__)
    {
        return Follow::detachRelations($this, 'favorites', $targets, $class);
    }

    /**
     * Toggle favorite an item or items.
     *
     * @param int|array|\Illuminate\Database\Eloquent\Model $targets
     * @param string                                        $class
     *
     * @return array
     */
    public function toggleFavorite($targets, $class = __CLASS__)
    {
        return Follow::toggleRelations($this, 'favorites', $targets, $class);
    }

    /**
     * Check if user is favorited given item.
     *
     * @param int|array|\Illuminate\Database\Eloquent\Model $target
     * @param string                                        $class
     *
     * @return bool
     */
    public function hasFavorited($target, $class = __CLASS__)
    {
        return Follow::isRelationExists($this, 'favorites', $target, $class);
    }

    /**
     * Return item favorites.
     *
     * @param string $class
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favorites($class = __CLASS__)
    {
        return $this->morphedByMany($class, config('follow.morph_prefix'), config('follow.followable_table'))
                    ->wherePivot('relation', '=', Follow::RELATION_FAVORITE)
                    ->withPivot('followable_type', 'relation', 'created_at');
    }
}
