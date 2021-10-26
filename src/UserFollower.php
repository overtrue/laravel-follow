<?php

namespace Overtrue\LaravelFollow;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Overtrue\LaravelFollow\Events\Followed;
use Overtrue\LaravelFollow\Events\Unfollowed;

/**
 * @property int|string $following_id;
 * @property int|string $follower_id;
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
