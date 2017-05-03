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

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use stdClass;

/**
 * Class Follow.
 */
class Follow
{
    const RELATION_LIKE = 'like';
    const RELATION_FOLLOW = 'follow';
    const RELATION_SUBSCRIBE = 'subscribe';
    const RELATION_FAVORITE = 'favorite';

    /**
     * @param string                                           $model
     * @param string                                           $relation
     * @param array|string|\Illuminate\Database\Eloquent\Model $target
     * @param string                                           $class
     *
     * @return bool
     */
    public static function isRelationExists($model, $relation, $target, $class = null)
    {
        $userModel = config('follow.user_model');
        $target = self::formatTargets($target, $class ?: $userModel);
        $key = $class ? 'followable_id' : 'user_id';
        $followableType = $class ? $target->classname : get_class($model);

        return $model->{$relation}($target->classname)
            ->where('followable_type', $followableType)
            ->where($key, head($target->ids))->exists();
    }

    /**
     * @param string                                           $model
     * @param string                                           $relation
     * @param array|string|\Illuminate\Database\Eloquent\Model $targets
     * @param string                                           $class
     *
     * @return array
     */
    public static function syncRelations($model, $relation, $targets, $class)
    {

        $relationName = self::getRelationTypeFromRelation($model->{$relation}());

        $targets = self::formatTargets($targets, $class, [
            'relation' => $relationName,
            'created_at' => Carbon::now()->format(config('follow.date_format', 'Y-m-d H:i:s')),
        ]);

        return $model->{$relation}($targets->classname)->sync($targets->targets);
    }

    /**
     * @param string                                           $model
     * @param string                                           $relation
     * @param array|string|\Illuminate\Database\Eloquent\Model $targets
     * @param string                                           $class
     *
     * @return array
     */
    public static function detachRelations($model, $relation, $targets, $class)
    {
        $targets = self::formatTargets($targets, $class);

        return $model->{$relation}($targets->classname)->detach($targets->ids);
    }

    /**
     * @param string                                           $model
     * @param string                                           $relation
     * @param array|string|\Illuminate\Database\Eloquent\Model $targets
     * @param string                                           $class
     *
     * @return array
     */
    public static function toggleRelations($model, $relation, $targets, $class)
    {
        $targets = self::formatTargets($targets, $class);

        return $model->{$relation}($targets->classname)->toggle($targets->ids);
    }

    /**
     * @param array|string|\Illuminate\Database\Eloquent\Model $targets
     * @param string                                           $classnane
     * @param array                                            $update
     *
     * @return \stdClass
     */
    public static function formatTargets($targets, $classnane, array $update = [])
    {
        $result = new stdClass();
        $result->classname = $classnane;

        if (!is_array($targets)) {
            $targets = [$targets];
        }

        $result->ids = array_map(function($target) use ($result){
            if ($target instanceof Model) {
                $result->classname = get_class($target);
                return $target->getKey();
            }

            return intval($target);
        }, $targets);

        $result->targets = array_combine($result->ids, array_pad([], count($result->ids), $update));

        return $result;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Relations\MorphToMany $relation
     *
     * @throws \Exception
     * @return array
     *
     */
    protected static function getRelationTypeFromRelation(MorphToMany $relation)
    {
        $wheres = array_pluck($relation->getQuery()->getQuery()->wheres, 'value', 'column');

        if (empty($wheres['followables.relation'])) {
            throw new \Exception('Invalid relation definition.');
        }

        return $wheres['followables.relation'];
    }
}