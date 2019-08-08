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

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Overtrue\LaravelFollow\Follow;

/**
 * Class Event.
 *
 * @author overtrue <i@overtrue.me>
 */
class Event
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $causer;

    public $relation;

    public $targets;

    public $class;

    /**
     * Event constructor.
     *
     * @param \Illuminate\Database\Eloquent\Model   $causer
     * @param \Overtrue\LaravelFollow\Events\string $relation
     * @param int|array                             $targets
     * @param \Overtrue\LaravelFollow\Events\string $class
     */
    public function __construct(Model $causer, string $relation, $targets, string $class)
    {
        $this->causer = $causer;
        $this->relation = $relation;
        $this->targets = $targets;
        $this->class = $class;
    }

    public function getRelationType()
    {
        return Follow::RELATION_TYPES[$this->relation];
    }

    public function getTargetsCollection()
    {
        return \forward_static_call([$this->targets->classname, 'find'], (array) $this->targets->ids);
    }
}
