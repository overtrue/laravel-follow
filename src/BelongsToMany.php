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

use Illuminate\Database\Eloquent\Relations\BelongsToMany as BaseBelongsToMany;

class BelongsToMany extends BaseBelongsToMany
{
    /**
     * Attach a model to the parent.
     *
     * @param mixed $id
     * @param array $attributes
     * @param bool  $touch
     */
    public function attach($id, array $attributes = [], $touch = true)
    {
        $this->parent->setPivotChanges('attach', $this->getRelationName(), [
            $id => $attributes,
        ]);

        if (false === $this->parent->firePivotAttachingEvent()) {
            return false;
        }

        $result = parent::attach($id, $attributes, $touch);

        $this->parent->firePivotAttachedEvent();

        return $result;
    }

    /**
     * Detach models from the relationship.
     *
     * @param mixed $ids
     * @param bool  $touch
     *
     * @return int
     */
    public function detach($ids = null, $touch = true)
    {
        if (is_null($ids)) {
            $ids = $this->query->pluck(
                $this->query->qualifyColumn($this->relatedKey)
            )->toArray();
        }

        $idsWithAttributes = collect($ids)->mapWithKeys(function ($id) {
            return [$id => []];
        })->all();

        $this->parent->setPivotChanges('detach', $this->getRelationName(), $idsWithAttributes);

        if (false === $this->parent->firePivotDetachingEvent()) {
            return false;
        }

        $result = parent::detach($ids, $touch);

        $this->parent->firePivotDetachedEvent();

        return $result;
    }

    /**
     * Update an existing pivot record on the table.
     *
     * @param mixed $id
     * @param array $attributes
     * @param bool  $touch
     *
     * @return int
     */
    public function updateExistingPivot($id, array $attributes, $touch = true)
    {
        $this->parent->setPivotChanges('update', $this->getRelationName(), [
            $id => $attributes,
        ]);

        if (false === $this->parent->firePivotUpdatingEvent()) {
            return false;
        }

        $result = parent::updateExistingPivot($id, $attributes, $touch);

        $this->parent->firePivotUpdatedEvent();

        return $result;
    }
}
