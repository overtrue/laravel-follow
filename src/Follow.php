<?php

/*
 * This file is part of the overtrue/laravel-follow
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
     * @param \Illuminate\Database\Eloquent\Model              $model
     * @param string                                           $relation
     * @param array|string|\Illuminate\Database\Eloquent\Model $target
     * @param string                                           $class
     *
     * @return bool
     */
    public static function isRelationExists(Model $model, $relation, $target, $class = null)
    {
        $target = self::formatTargets($target, $class ?: config('follow.user_model'));

        return $model->{$relation}($target->classname)
                        ->where($class ? 'followable_id' : 'user_id', head($target->ids))->exists();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model              $model
     * @param string                                           $relation
     * @param array|string|\Illuminate\Database\Eloquent\Model $targets
     * @param string                                           $class
     *
     * @return array
     */
    public static function attachRelations(Model $model, $relation, $targets, $class)
    {
        $targets = self::attachPivotsFromRelation($model->{$relation}(), $targets, $class);

        return $model->{$relation}($targets->classname)->sync($targets->targets, false);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model              $model
     * @param string                                           $relation
     * @param array|string|\Illuminate\Database\Eloquent\Model $targets
     * @param string                                           $class
     *
     * @return array
     */
    public static function detachRelations(Model $model, $relation, $targets, $class)
    {
        $targets = self::formatTargets($targets, $class);

        return $model->{$relation}($targets->classname)->detach($targets->ids);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model              $model
     * @param string                                           $relation
     * @param array|string|\Illuminate\Database\Eloquent\Model $targets
     * @param string                                           $class
     *
     * @return array
     */
    public static function toggleRelations(Model $model, $relation, $targets, $class)
    {
        $targets = self::attachPivotsFromRelation($model->{$relation}(), $targets, $class);

        return $model->{$relation}($targets->classname)->toggle($targets->targets);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Relations\MorphToMany $morph
     * @param array|string|\Illuminate\Database\Eloquent\Model    $targets
     * @param string                                              $class
     *
     * @return \stdClass
     */
    public static function attachPivotsFromRelation(MorphToMany $morph, $targets, $class)
    {
        return self::formatTargets($targets, $class, [
            'relation' => self::getRelationTypeFromRelation($morph),
            'created_at' => Carbon::now()->format(config('follow.date_format', 'Y-m-d H:i:s')),
        ]);
    }

    /**
     * @param array|string|\Illuminate\Database\Eloquent\Model $targets
     * @param string                                           $classname
     * @param array                                            $update
     *
     * @return \stdClass
     */
    public static function formatTargets($targets, $classname, array $update = [])
    {
        $result = new stdClass();
        $result->classname = $classname;

        if (!is_array($targets)) {
            $targets = [$targets];
        }

        $result->ids = array_map(function ($target) use ($result) {
            if ($target instanceof Model) {
                $result->classname = get_class($target);

                return $target->getKey();
            }

            return intval($target);
        }, $targets);

        $result->targets = empty($update) ? $result->ids : array_combine($result->ids, array_pad([], count($result->ids), $update));

        return $result;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Relations\MorphToMany $relation
     *
     * @throws \Exception
     *
     * @return array
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
