<?php

namespace Overtrue\LaravelFollow\Events;

use Overtrue\LaravelFollow\UserFollower;

class Event
{
    /**
     * @var int|string
     */
    public $followingId;

    /**
     * @var int|string
     */
    public $followerId;

    protected UserFollower $relation;

    public function __construct(UserFollower $relation)
    {
        $this->relation = $relation;
        $this->followerId = $relation->follower_id;
        $this->followingId = $relation->following_id;
    }
}
