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

use Illuminate\Database\Eloquent\Relations\Pivot;
use Overtrue\LaravelFollow\Events\Followed;
use Overtrue\LaravelFollow\Events\Unfollowed;

/**
 * Class UserFollower.
 *
 * @property int $following_id;
 * @property int $follower_id;
 */
class UserFollower extends Pivot
{
    /**
     * @var string[]
     */
    protected $dispatchesEvents = [
        'created' => Followed::class,
        'deleted' => Unfollowed::class,
    ];
}
