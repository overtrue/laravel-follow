<?php

namespace Overtrue\LaravelFollow\Traits;

use function config;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Overtrue\LaravelFollow\Traits\Follower as Follower;

/**
 * @property Collection $followables
 * @property Collection $followers
 */
trait Followable
{
    public function needsToApproveFollowRequests(): bool
    {
        return false;
    }

    public function rejectFollowRequestFrom(Model $follower): void
    {
        if (! in_array(Follower::class, \class_uses($follower))) {
            throw new \InvalidArgumentException('The model must use the Follower trait.');
        }

        $this->followables()->followedBy($follower)->get()->each->delete();
    }

    public function acceptFollowRequestFrom(Model $follower): void
    {
        if (! in_array(Follower::class, \class_uses($follower))) {
            throw new \InvalidArgumentException('The model must use the Follower trait.');
        }

        $this->followables()->followedBy($follower)->get()->each->update(['accepted_at' => \now()]);
    }

    public function isFollowedBy(Model $follower): bool
    {
        if (! in_array(Follower::class, \class_uses($follower))) {
            throw new \InvalidArgumentException('The model must use the Follower trait.');
        }

        if ($this->relationLoaded('followables')) {
            return $this->followables->whereNotNull('accepted_at')->contains($follower);
        }

        return $this->followables()->accepted()->followedBy($follower)->exists();
    }

    public function scopeOrderByFollowersCount($query, string $direction = 'desc')
    {
        return $query->withCount('followers')->orderBy('followers_count', $direction);
    }

    public function scopeOrderByFollowersCountDesc($query)
    {
        return $this->scopeOrderByFollowersCount($query, 'desc');
    }

    public function scopeOrderByFollowersCountAsc($query)
    {
        return $this->scopeOrderByFollowersCount($query, 'asc');
    }

    public function followables(): HasMany
    {
        /**
         * @var Model $this
         */
        return $this->hasMany(
            config('follow.followables_model', \Overtrue\LaravelFollow\Followable::class),
            'followable_id',
        )->where('followable_type', $this->getMorphClass());
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(
            config('auth.providers.users.model'),
            config('follow.followables_table', 'followables'),
            'followable_id',
            config('follow.user_foreign_key', 'user_id')
        )->where('followable_type', $this->getMorphClass())
            ->withPivot(['accepted_at']);
    }

    public function approvedFollowers(): BelongsToMany
    {
        return $this->followers()->whereNotNull('accepted_at');
    }

    public function notApprovedFollowers(): BelongsToMany
    {
        return $this->followers()->whereNull('accepted_at');
    }
}
