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
 * Trait CanBeFavorited.
 */
trait CanBeFavorited
{
    /**
     * Check if user is favorited by given user.
     *
     * @param int $user
     *
     * @return bool
     */
    public function isFavoritedBy($user)
    {
        return Follow::isRelationExists($this, 'favoriters', $user);
    }

    /**
     * Return favoriters.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function favoriters()
    {
        return $this->morphToMany(config('follow.user_model'), config('follow.morph_prefix'), config('follow.followable_table'))
                    ->wherePivot('relation', '=', Follow::RELATION_FAVORITE)
                    ->withPivot('followable_type', 'relation', 'created_at');
    }
}
