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

class CanBeFollowedTest extends TestCase
{
    public function test_user_can_follow_by_id()
    {
        $user1 = User::find(1);
        $user2 = User::find(2);

        $user1->follow($user2->id);

        $this->assertCount(1, $user2->followers);
    }

    public function test_user_can_follow_multiple_users()
    {
        $user1 = User::find(1);
        $user2 = User::find(2);
        $user3 = User::find(3);

        $user1->follow([$user2->id, $user3->id]);

        $this->assertCount(1, $user2->followers);
        $this->assertCount(1, $user3->followers);
    }

    public function test_is_followed_by()
    {
        $user1 = User::find(1);
        $user2 = User::find(2);

        $user1->follow($user2->id);

        $this->assertTrue($user2->isFollowedBy($user1->id));
    }

    public function test_user_can_follow_other_by_id()
    {
        $user = User::find(1);
        $other = Other::find(1);

        $user->follow($other);

        $this->assertCount(1, $other->followers);
    }

    public function test_is_followed_by_user()
    {
        $user = User::find(1);
        $other = Other::find(1);

        $user->follow($other);

        $this->assertTrue($other->isFollowedBy($user));
    }
}
