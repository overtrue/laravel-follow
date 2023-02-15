<?php

namespace Overtrue\LaravelFollow\Traits;

use function abort_if;
use function class_uses;
use function collect;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Enumerable;
use Illuminate\Support\LazyCollection;
use function in_array;
use InvalidArgumentException;
use function is_array;
use function iterator_to_array;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @property Collection $followings
 */
trait Follower
{
    #[ArrayShape(['pending' => 'mixed'])]
    public function follow(Model $followable): array
    {
        if ($followable->is($this)) {
            throw new InvalidArgumentException('Cannot follow yourself.');
        }

        if (! in_array(Followable::class, class_uses($followable))) {
            throw new InvalidArgumentException('The followable model must use the Followable trait.');
        }

        /** @var \Illuminate\Database\Eloquent\Model|\Overtrue\LaravelFollow\Traits\Followable $followable */
        $isPending = $followable->needsToApproveFollowRequests() ?: false;

        $this->followings()->updateOrCreate([
            'followable_id' => $followable->getKey(),
            'followable_type' => $followable->getMorphClass(),
        ], [
            'accepted_at' => $isPending ? null : now(),
        ]);

        return ['pending' => $isPending];
    }

    public function unfollow(Model $followable): void
    {
        if (! in_array(Followable::class, class_uses($followable))) {
            throw new InvalidArgumentException('The followable model must use the Followable trait.');
        }

        $this->followings()->of($followable)->get()->each->delete();
    }

    public function toggleFollow(Model $followable): void
    {
        $this->isFollowing($followable) ? $this->unfollow($followable) : $this->follow($followable);
    }

    public function isFollowing(Model $followable): bool
    {
        if (! in_array(Followable::class, class_uses($followable))) {
            throw new InvalidArgumentException('The followable model must use the Followable trait.');
        }

        if ($this->relationLoaded('followings')) {
            return $this->followings
                ->whereNotNull('accepted_at')
                ->where('followable_id', $followable->getKey())
                ->where('followable_type', $followable->getMorphClass())
                ->isNotEmpty();
        }

        return $this->followings()->of($followable)->accepted()->exists();
    }

    public function hasRequestedToFollow(Model $followable): bool
    {
        if (! in_array(\Overtrue\LaravelFollow\Traits\Followable::class, \class_uses($followable))) {
            throw new InvalidArgumentException('The followable model must use the Followable trait.');
        }

        if ($this->relationLoaded('followings')) {
            return $this->followings->whereNull('accepted_at')
                ->where('followable_id', $followable->getKey())
                ->where('followable_type', $followable->getMorphClass())
                ->isNotEmpty();
        }

        return $this->followings()->of($followable)->notAccepted()->exists();
    }

    public function followings(): HasMany
    {
        /**
         * @var Model $this
         */
        return $this->hasMany(
            config('follow.followables_model', \Overtrue\LaravelFollow\Followable::class),
            config('follow.user_foreign_key', 'user_id'),
            $this->getKeyName()
        );
    }

    public function approvedFollowings(): HasMany
    {
        return $this->followings()->accepted();
    }

    public function notApprovedFollowings(): HasMany
    {
        return $this->followings()->notAccepted();
    }

    public function attachFollowStatus($followables, callable $resolver = null)
    {
        $returnFirst = false;

        switch (true) {
            case $followables instanceof Model:
                $returnFirst = true;
                $followables = collect([$followables]);
                break;
            case $followables instanceof LengthAwarePaginator:
                $followables = $followables->getCollection();
                break;
            case $followables instanceof Paginator:
            case $followables instanceof CursorPaginator:
                $followables = collect($followables->items());
                break;
            case $followables instanceof LazyCollection:
                $followables = collect(iterator_to_array($followables->getIterator()));
                break;
            case is_array($followables):
                $followables = collect($followables);
                break;
        }

        abort_if(! ($followables instanceof Enumerable), 422, 'Invalid $followables type.');

        $followed = $this->followings()->get();

        $followables->map(function ($followable) use ($followed, $resolver) {
            $resolver = $resolver ?? fn ($m) => $m;
            $followable = $resolver($followable);

            if ($followable && in_array(Followable::class, class_uses($followable))) {
                $item = $followed->where('followable_id', $followable->getKey())
                                 ->where('followable_type', $followable->getMorphClass())
                                 ->first();
                $followable->setAttribute('followed_at', $item ? $item->created_at : null);
                $followable->setAttribute('follow_accepted_at', $item ? $item->accepted_at : null);
            }
        });

        return $returnFirst ? $followables->first() : $followables;
    }
}
