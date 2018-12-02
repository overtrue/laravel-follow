<?php

/*
 * This file is part of the overtrue/laravel-follow
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\LaravelFollow\Test\Traits;

use Overtrue\LaravelFollow\Test\Other;
use Overtrue\LaravelFollow\Test\TestCase;
use Overtrue\LaravelFollow\Test\User;

class CanFollowTest extends TestCase
{
    public function test_user_can_follow_by_id()
    {
        $user1 = User::find(1);
        $user2 = User::find(2);

        $user1->follow($user2->id);

        $this->assertCount(1, $user1->followings);
    }

    public function test_user_can_follow_multiple_users()
    {
        $user1 = User::find(1);
        $user2 = User::find(2);
        $user3 = User::find(3);

        $user1->follow([$user2->id, $user3->id]);

        $this->assertCount(2, $user1->followings);
    }

    public function test_unfollow_user()
    {
        $user1 = User::find(1);
        $user2 = User::find(2);

        $user1->follow($user2->id);
        $this->assertCount(1, $user2->followers);
        $user1->unfollow($user2->id);
        $this->assertCount(0, $user1->followings);
    }

    public function test_is_following()
    {
        $user1 = User::find(1);
        $user2 = User::find(2);

        $user1->follow($user2->id);

        $this->assertTrue($user1->isFollowing($user2->id));
    }

    public function test_user_can_follow_other_by_id()
    {
        $user = User::find(1);
        $other = Other::find(1);

        $user->follow($other);

        $this->assertCount(1, $user->followings(Other::class)->get());
    }

    public function test_unfollow_other()
    {
        $user = User::find(1);
        $other = Other::find(1);

        $user->follow($other);
        $user->unfollow($other);

        $this->assertCount(0, $user->followings);
    }

    public function test_is_following_other()
    {
        $user = User::find(1);
        $other = Other::find(1);

        $user->follow($other);

        $this->assertTrue($user->isFollowing($other));
    }

    public function test_following_each_other()
    {
        $user1 = User::find(1);
        $user2 = User::find(2);

        $user1->follow($user2);

        $this->assertFalse($user1->areFollowingEachOther($user2));

        $user2->follow($user1);
        $this->assertTrue($user1->areFollowingEachOther($user2));
    }

    public function test_eager_loading()
    {
        $user1 = User::find(1);
        $user2 = User::find(2);

        $user1->follow($user2);
        $user2->follow($user1);

        // eager loading
        $user2 = User::find(2)->load(['followings', 'followers']);
        $this->assertTrue($user2->isFollowedBy($user1));

        // without eager loading
        $this->assertTrue($user1->isFollowedBy($user2));
    }
}
