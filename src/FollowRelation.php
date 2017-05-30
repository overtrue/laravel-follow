<?php

/*
 * This file is part of the overtrue/laravel-follow.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\LaravelFollow;


use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

/**
 * Class FollowRelation
 */
class FollowRelation extends Model
{
    /**
     * @var string
     */
    protected $table;

    /**
     * @var array
     */
    protected $with = ['object'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function object()
    {
        return $this->morphTo(config('follow.morph_prefix', 'followable'));
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder  $query
     * @param string|null                            $type
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePopular($query, $type = null)
    {
        $query->select('followable_id', 'followable_type', \DB::raw('COUNT(*) AS count'))
                     ->groupBy('followable_id', 'followable_type')
                     ->orderByDesc('count');

        if ($type) {
            $query->where('followable_type', $this->normalizeFollowableType($type));
        }

        return $query;
    }

    /**
     * @inheritdoc
     */
    public function getTable()
    {
        if (!$this->table) {
            $this->table = config('follow.followable_table', 'followables');
        }

        return parent::getTable();
    }

    /**
     * @inheritdoc
     */
    public function getDates()
    {
        return [parent::CREATED_AT];
    }

    /**
     * @param string $type
     *
     * @throws \InvalidArgumentException
     * @return string
     *
     */
    protected function normalizeFollowableType($type)
    {
        if (class_exists($type)) {
            return $type;
        }

        $namespace = config('follow.model_namespace', 'App');

        $modelName = $namespace.'\\'.studly_case($type);

        if (!class_exists($modelName)) {
            throw new InvalidArgumentException("Model {$modelName} not exists. Please check your config 'follow.model_namespace'.");
        }

        return $modelName;
    }
}