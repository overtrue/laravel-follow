<?php


namespace Overtrue\LaravelFollow\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Overtrue\LaravelFollow\Follow;

/**
 * Class Event
 *
 * @author overtrue <i@overtrue.me>
 */
class Event
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

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
        return \forward_static_call([$this->class, 'find'], (array) $this->targets);
    }
}