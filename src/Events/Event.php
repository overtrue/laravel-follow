<?php

/*
 * This file is part of the overtrue/laravel-followable.
 *
 * (c) overtrue <anzhengchao@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Overtrue\LaravelFollow\Events;

use Illuminate\Database\Eloquent\Model;
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
