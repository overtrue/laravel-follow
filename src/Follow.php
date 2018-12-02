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
use Illuminate\Database\Eloquent\SoftDeletes;
use Overtrue\LaravelFollow\Events\RelationAttached;
use Overtrue\LaravelFollow\Events\RelationAttaching;
use Overtrue\LaravelFollow\Events\RelationDetached;
use Overtrue\LaravelFollow\Events\RelationDetaching;
use Overtrue\LaravelFollow\Events\RelationToggled;
use Overtrue\LaravelFollow\Events\RelationToggling;
use stdClass;

/**
 * Class Follow.
 */
class Follow
{
    use SoftDeletes;

    const RELATION_LIKE = 'like';

    const RELATION_FOLLOW = 'follow';

    const RELATION_BOOKMARK = 'bookmark';

    const RELATION_SUBSCRIBE = 'subscribe';

    const RELATION_FAVORITE = 'favorite';

    const RELATION_UPVOTE = 'upvote';

    const RELATION_DOWNVOTE = 'downvote';

    const RELATION_TYPES = [
        'likes' => 'like',
        'likers' => 'like',
        'fans' => 'like',
        'followings' => 'follow',
        'followers' => 'follow',
        'favoriters' => 'favorite',
        'favorites' => 'favorite',
        'bookmarkers' => 'bookmark',
        'bookmarks' => 'bookmark',
        'subscriptions' => 'subscribe',
        'subscribers' => 'subscribe',
        'upvotes' => 'upvote',
        'upvoters' => 'upvote',
        'downvotes' => 'downvote',
        'downvoters' => 'downvote',
    ];

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

        if ($model->relationLoaded($relation)) {
            return $model->{$relation}->where('id', head($target->ids))->isNotEmpty();
        }

        return $model->{$relation}($target->classname)->where('id', head($target->ids))->exists();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model              $model
     * @param string                                           $relation
     * @param array|string|\Illuminate\Database\Eloquent\Model $targets
     * @param string                                           $class
     *
     * @throws \Exception
     *
     * @return array
     */
    public static function attachRelations(Model $model, $relation, $targets, $class)
    {
        if (false === \event(new RelationAttaching($model, $relation, $targets, $class))) {
            return false;
        }

        $targets = self::attachPivotsFromRelation($model->{$relation}(), $targets, $class);

        if (false === \event(new RelationAttached($model, $relation, $targets, $class))) {
            return false;
        }

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
        if (false === \event(new RelationDetaching($model, $relation, $targets, $class))) {
            return false;
        }

        $targets = self::formatTargets($targets, $class);

        if (false === \event(new RelationDetached($model, $relation, $targets, $class))) {
            return false;
        }

        return $model->{$relation}($targets->classname)->detach($targets->ids);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model              $model
     * @param string                                           $relation
     * @param array|string|\Illuminate\Database\Eloquent\Model $targets
     * @param string                                           $class
     *
     * @throws \Exception
     *
     * @return array
     */
    public static function toggleRelations(Model $model, $relation, $targets, $class)
    {
        if (false === \event(new RelationToggling($model, $relation, $targets, $class))) {
            return false;
        }

        $targets = self::attachPivotsFromRelation($model->{$relation}(), $targets, $class);

        $results = $model->{$relation}($targets->classname)->toggle($targets->targets);

        if (false === \event(new RelationToggled($model, $relation, $targets, $class, $results))) {
            return false;
        }

        return $results;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Relations\MorphToMany $morph
     * @param array|string|\Illuminate\Database\Eloquent\Model    $targets
     * @param string                                              $class
     *
     * @throws \Exception
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
        if (!\array_key_exists($relation->getRelationName(), self::RELATION_TYPES)) {
            throw new \Exception('Invalid relation definition.');
        }

        return self::RELATION_TYPES[$relation->getRelationName()];
    }

    /**
     * @param string $field
     *
     * @return string
     */
    protected static function tablePrefixedField($field)
    {
        return \sprintf('%s.%s', config('follow.followable_table'), $field);
    }
}
