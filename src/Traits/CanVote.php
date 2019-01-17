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
 * Trait CanVote.
 */
trait CanVote
{
    /**
     * Vote an item or items.
     *
     * @param int|array|\Illuminate\Database\Eloquent\Model $targets
     * @param string                                        $type
     * @param string                                        $class
     *
     * @throws \Exception
     *
     * @return array
     */
    public function vote($targets, $type = 'upvote', $class = __CLASS__)
    {
        $this->cancelVote($targets);

        return Follow::attachRelations($this, str_plural($type), $targets, $class);
    }

    /**
     * Upvote an item or items.
     *
     * @param int|array|\Illuminate\Database\Eloquent\Model $targets
     * @param string                                        $class
     *
     * @throws \Exception
     *
     * @return array
     */
    public function upvote($targets, $class = __CLASS__)
    {
        return $this->vote($targets, 'upvote', $class);
    }

    /**
     * Downvote an item or items.
     *
     * @param int|array|\Illuminate\Database\Eloquent\Model $targets
     * @param string                                        $class
     *
     * @throws \Exception
     *
     * @return array
     */
    public function downvote($targets, $class = __CLASS__)
    {
        return $this->vote($targets, 'downvote', $class);
    }

    /**
     * Cancel vote for an item or items.
     *
     * @param int|array|\Illuminate\Database\Eloquent\Model $targets
     * @param string                                        $class
     *
     * @return \Overtrue\LaravelFollow\Traits\CanVote
     */
    public function cancelVote($targets, $class = __CLASS__)
    {
        $this->hasUpvoted($targets) && Follow::detachRelations($this, 'upvotes', $targets, $class);
        $this->hasDownvoted($targets) && Follow::detachRelations($this, 'downvotes', $targets, $class);

        return $this;
    }

    /**
     * Check if user is upvoted given item.
     *
     * @param int|array|\Illuminate\Database\Eloquent\Model $target
     * @param string                                        $class
     *
     * @return bool
     */
    public function hasUpvoted($target, $class = __CLASS__)
    {
        return Follow::isRelationExists($this, 'upvotes', $target, $class);
    }

    /**
     * Check if user is downvoted given item.
     *
     * @param int|array|\Illuminate\Database\Eloquent\Model $target
     * @param string                                        $class
     *
     * @return bool
     */
    public function hasDownvoted($target, $class = __CLASS__)
    {
        return Follow::isRelationExists($this, 'downvotes', $target, $class);
    }

    /**
     * Return item votes.
     *
     * @param string $class
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function votes($class = __CLASS__)
    {
        return $this->morphedByMany($class, config('follow.morph_prefix'), config('follow.followable_table'))
                    ->wherePivotIn('relation', [Follow::RELATION_UPVOTE, Follow::RELATION_DOWNVOTE])
                    ->withPivot('followable_type', 'relation', 'created_at');
    }

    /**
     * Return item upvotes.
     *
     * @param string $class
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function upvotes($class = __CLASS__)
    {
        return $this->morphedByMany($class, config('follow.morph_prefix'), config('follow.followable_table'))
                    ->wherePivot('relation', '=', Follow::RELATION_UPVOTE)
                    ->withPivot('followable_type', 'relation', 'created_at');
    }

    /**
     * Return item downvotes.
     *
     * @param string $class
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function downvotes($class = __CLASS__)
    {
        return $this->morphedByMany($class, config('follow.morph_prefix'), config('follow.followable_table'))
                    ->wherePivot('relation', '=', Follow::RELATION_DOWNVOTE)
                    ->withPivot('followable_type', 'relation', 'created_at');
    }
}
