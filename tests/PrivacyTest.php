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
 * Class PrivacyTest.
 */
class PrivacyTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        config(['auth.providers.users.model' => User::class]);
    }

    public function test_following_private_user_sets_request_pending()
    {
        $user1 = User::create(['name' => 'user1']);
        $user2 = User::create(['name' => 'user2', 'private' => true]);

        $user1->follow($user2);

        $this->assertTrue($user1->hasRequestedToFollow($user2));
        $this->assertFalse($user2->isFollowedBy($user1));
    }

    public function test_pending_request_can_be_accepted()
    {
        $user1 = User::create(['name' => 'user1']);
        $user2 = User::create(['name' => 'user2', 'private' => true]);

        $user1->follow($user2);

        $user2->acceptFollowRequestFrom($user1);

        $this->assertFalse($user1->hasRequestedToFollow($user2));
        $this->assertTrue($user2->isFollowedBy($user1));
    }

    public function test_pending_request_can_be_rejected()
    {
        $user1 = User::create(['name' => 'user1']);
        $user2 = User::create(['name' => 'user2', 'private' => true]);

        $user1->follow($user2);

        $user2->rejectFollowRequestFrom($user1);

        $this->assertFalse($user1->hasRequestedToFollow($user2));
        $this->assertFalse($user2->isFollowedBy($user1));
    }

    public function test_following_private_user_sets_request_pending_with_eager_loading()
    {
        $user1 = User::create(['name' => 'user1']);
        $user2 = User::create(['name' => 'user2', 'private' => true]);

        $user1->follow($user2);

        $user1->load('followings');

        $this->assertTrue($user1->hasRequestedToFollow($user2));
        $this->assertFalse($user2->isFollowedBy($user1));
    }
}
