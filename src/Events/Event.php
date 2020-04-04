<?php

/*
 * This file is part of the overtrue/laravel-follow
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\LaravelFollow\Events;

use Overtrue\LaravelFollow\UserFollower;

class Event
{
    /**
     * @var int
     */
    public $followingId;

    /**
     * @var int
     */
    public $followerId;

    /**
     * @var \Overtrue\LaravelFollow\UserFollower
     */
    protected $relation;

    /**
     * Event constructor.
     *
     * @param \Overtrue\LaravelFollow\UserFollower $relation
     */
    public function __construct(UserFollower $relation)
    {
        $this->relation = $relation;
        $this->followerId = $relation->follower_id;
        $this->followingId = $relation->following_id;
    }
}
