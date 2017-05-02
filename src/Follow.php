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
     * @param string  $model
     * @param string  $relation
     * @param array|string|\Illuminate\Database\Eloquent\Model      $target
     * @param string                                                $relationName
     * @param string                                                $class
     *
     * @return bool
     */
    public static function isRelationExists($model, $relation, $target, $relationName, $class = null)
    {
        $userModel = config('follow.user_model');
        $target = self::formatTargets($target, $class ?: $userModel);
        $key = $class ? 'followable_id' : 'user_id';
        $followableType = $class ? $target->classname : get_class($model);

        return $model->{$relation}($target->classname)
                ->where('relation', $relationName)
                ->where('followable_type', $followableType)
                ->where($key, head($target->ids))->exists();
    }

    /**
     * @param string $model
     * @param string $relation
     * @param array|string|\Illuminate\Database\Eloquent\Model $targets
     * @param string $relationName
     * @param string $class
     *
     * @return int
     */
    public static function syncRelations($model, $relation, $targets, $relationName, $class)
    {
        $targets = self::formatTargets($targets, $class, [
            'relation' => $relationName,
            'created_at' => Carbon::now()->format(config('follow.date_format', 'Y-m-d H:i:s')),
        ]);

        return $model->{$relation}($targets->classname)->sync($targets->targets);
    }

    /**
     * @param string $model
     * @param string $relation
     * @param array|string|\Illuminate\Database\Eloquent\Model $targets
     * @param string $class
     *
     * @return mixed
     */
    public static function detachRelations($model, $relation, $targets, $class)
    {
        $targets = self::formatTargets($targets, $class);

        return $model->{$relation}($targets->classname)->detach($targets->ids);
    }

    /**
     * @param array|string|\Illuminate\Database\Eloquent\Model $targets
     * @param string                                           $className
     * @param array                                            $update
     *
     * @return \stdClass
     */
    public static function formatTargets($targets, $className, array $update = [])
    {
        $result = new stdClass();
        $result->classname = $className;

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
}