<?php

namespace Overtrue\LaravelFollow\Events;

use Overtrue\LaravelFollow\UserFollower;

class Event
{
    public int $followingId;

    public int $followerId;

    protected UserFollower $relation;

    public function __construct(UserFollower $relation)
    {
        $this->relation = $relation;
        $this->followerId = $relation->follower_id;
        $this->followingId = $relation->following_id;
    }
}
