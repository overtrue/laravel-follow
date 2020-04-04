<?php

/*
 * This file is part of the overtrue/laravel-follow.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Tests;

use Illuminate\Support\Facades\Event;
use Overtrue\LaravelFollow\Events\Followed;
use Overtrue\LaravelFollow\Events\Unfollowed;

/**
 * Class FeatureTest.
 */
class FeatureTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Event::fake();

        config(['auth.providers.users.model' => User::class]);
    }

    public function testBasicFeatures()
    {
        $user1 = User::create(['name' => 'user1']);
        $user2 = User::create(['name' => 'user2']);

        $user1->follow($user2);

        Event::assertDispatched(Followed::class, function ($event) use ($user1, $user2) {
            return $event->followingId === $user2->id && $event->followerId === $user1->id;
        });

        $this->assertTrue($user1->isFollowing($user2));
        $this->assertTrue($user2->isFollowedBy($user1));

        $user1->unfollow($user2);

        Event::assertDispatched(Unfollowed::class, function ($event) use ($user1, $user2) {
            return $event->followingId === $user2->id && $event->followerId === $user1->id;
        });
    }

    public function test_unfollow_features()
    {
        $user1 = User::create(['name' => 'user1']);
        $user2 = User::create(['name' => 'user2']);
        $user3 = User::create(['name' => 'user3']);
        $user4 = User::create(['name' => 'user4']);

        $user1->follow($user4);
        $user1->follow($user2);
        $user2->follow($user4);
        $user3->follow($user4);

        $this->assertSame(3, $user4->followers()->count());
        $this->assertSame(2, $user1->followings()->count());

        $user1->unfollow($user4);
        $this->assertFalse($user1->isFollowing($user4));
        $this->assertTrue($user2->isFollowing($user4));
        $this->assertTrue($user3->isFollowing($user4));
    }

    public function test_eager_loading()
    {
        $user1 = User::create(['name' => 'user1']);
        $user2 = User::create(['name' => 'user2']);
        $user3 = User::create(['name' => 'user3']);
        $user4 = User::create(['name' => 'user4']);

        $user1->follow($user2);
        $user1->follow($user3);
        $user1->follow($user4);
        $user2->follow($user4);
        $user3->follow($user4);

        // without eager loading
        $sqls = $this->getQueryLog(function () use ($user1, $user2, $user3, $user4) {
            $user1->isFollowing($user2);
            $user1->isFollowing($user3);
            $user1->isFollowing($user4);
        });

        $this->assertSame(3, $sqls->count());

        // -- following
        // with eager loading
        $user1->load('followings');
        $sqls = $this->getQueryLog(function () use ($user1, $user2, $user3, $user4) {
            $user1->isFollowing($user2);
            $user1->isFollowing($user3);
            $user1->isFollowing($user4);
        });
        $this->assertSame(0, $sqls->count());

        // -- followers
        // without eager loading
        $sqls = $this->getQueryLog(function () use ($user1, $user2, $user3, $user4) {
            $user4->isFollowedBy($user1);
            $user4->isFollowedBy($user2);
            $user4->isFollowedBy($user3);
        });

        $this->assertSame(3, $sqls->count());

        // with eager loading
        $user4->load('followers');
        $sqls = $this->getQueryLog(function () use ($user1, $user2, $user3, $user4) {
            $user4->isFollowedBy($user1);
            $user4->isFollowedBy($user2);
            $user4->isFollowedBy($user3);
        });
        $this->assertSame(0, $sqls->count());

        // -- follow each other
        $user4->follow($user1);
        // without loading
        $sqls = $this->getQueryLog(function () use ($user1, $user2, $user3, $user4) {
            $user1->areFollowingEachOther($user4);
        });
        $this->assertSame(1, $sqls->count());

        // with eager loading
        $user1->load('followings', 'followers');
        $sqls = $this->getQueryLog(function () use ($user1, $user2, $user3, $user4) {
            $user1->areFollowingEachOther($user4);
        });
        $this->assertSame(0, $sqls->count());
    }

    /**
     * @param \Closure $callback
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getQueryLog(\Closure $callback): \Illuminate\Support\Collection
    {
        $sqls = \collect([]);
        \DB::listen(function ($query) use ($sqls) {
            $sqls->push(['sql' => $query->sql, 'bindings' => $query->bindings]);
        });

        $callback();

        return $sqls;
    }
}
