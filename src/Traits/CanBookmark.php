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
 * Trait CanBookmark.
 */
trait CanBookmark
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
    public function bookmark($targets, $class = __CLASS__)
    {
        return Follow::attachRelations($this, 'bookmarkings', $targets, $class);
    }

    /**
     * Unbookmark an item or items.
     *
     * @param int|array|\Illuminate\Database\Eloquent\Model $targets
     * @param string                                        $class
     *
     * @return array
     */
    public function unbookmark($targets, $class = __CLASS__)
    {
        return Follow::detachRelations($this, 'bookmarkings', $targets, $class);
    }

    /**
     * Toggle bookmark an item or items.
     *
     * @param int|array|\Illuminate\Database\Eloquent\Model $targets
     * @param string                                        $class
     *
     * @return array
     *
     * @throws \Exception
     */
    public function toggleBookmark($targets, $class = __CLASS__)
    {
        return Follow::toggleRelations($this, 'bookmarkings', $targets, $class);
    }

    /**
     * Check if user is bookmarking given item.
     *
     * @param int|array|\Illuminate\Database\Eloquent\Model $target
     * @param string                                        $class
     *
     * @return bool
     */
    public function isBookmarking($target, $class = __CLASS__)
    {
        return Follow::isRelationExists($this, 'bookmarkings', $target, $class);
    }

    /**
     * Return item bookmarkings.
     *
     * @param string $class
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function bookmarkings($class = __CLASS__)
    {
        return $this->morphedByMany($class, config('follow.morph_prefix'), config('follow.followable_table'))
                    ->wherePivot('relation', '=', Follow::RELATION_BOOKMARK)
                    ->withPivot('followable_type', 'relation', 'created_at');
    }
}
