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

/**
 * Class RelationToggled.
 *
 * @author overtrue <i@overtrue.me>
 */
class RelationToggled extends Event
{
    public $results = [];

    public $attached = [];

    public $detached = [];

    /**
     * RelationToggled constructor.
     *
     * @param \Illuminate\Database\Eloquent\Model $causer
     * @param                                     $relation
     * @param                                     $targets
     * @param                                     $class
     * @param array                               $results
     */
    public function __construct(\Illuminate\Database\Eloquent\Model $causer, $relation, $targets, $class, array $results = [])
    {
        parent::__construct($causer, $relation, $targets, $class);

        $this->results = $results;
        $this->attached = $results['attached'] ?? [];
        $this->detached = $results['detached'] ?? [];
    }
}
