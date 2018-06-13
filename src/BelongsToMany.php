<?php

namespace Overtrue\LaravelFollow;

use Illuminate\Database\Eloquent\Relations\BelongsToMany as BaseBelongsToMany;

class BelongsToMany extends BaseBelongsToMany
{
    /**
     * Attach a model to the parent.
     *
     * @param  mixed  $id
     * @param  array  $attributes
     * @param  bool   $touch
     * @return void
     */
    public function attach($id, array $attributes = [], $touch = true)
    {
        $this->parent->setPivotChanges('attach', $this->getRelationName(), [
            $id => $attributes,
        ]);

        if ($this->parent->firePivotAttachingEvent() === false) {
            return false;
        }

        $result = parent::attach($id, $attributes, $touch);

        $this->parent->firePivotAttachedEvent();

        return $result;
    }

    /**
     * Detach models from the relationship.
     *
     * @param  mixed  $ids
     * @param  bool  $touch
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

        if ($this->parent->firePivotDetachingEvent() === false) {
            return false;
        }

        $result = parent::detach($ids, $touch);

        $this->parent->firePivotDetachedEvent();

        return $result;
    }

    /**
     * Update an existing pivot record on the table.
     *
     * @param  mixed  $id
     * @param  array  $attributes
     * @param  bool   $touch
     * @return int
     */
    public function updateExistingPivot($id, array $attributes, $touch = true)
    {
        $this->parent->setPivotChanges('update', $this->getRelationName(), [
            $id => $attributes,
        ]);

        if ($this->parent->firePivotUpdatingEvent() === false) {
            return false;
        }

        $result = parent::updateExistingPivot($id, $attributes, $touch);

        $this->parent->firePivotUpdatedEvent();

        return $result;
    }
}
