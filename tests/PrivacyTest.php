<?php

namespace Tests;

use Illuminate\Support\Facades\Event;
use Overtrue\LaravelFollow\Events\Followed;
use Overtrue\LaravelFollow\Events\Unfollowed;

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

    public function test_approved_scopes()
    {
        $user1 = User::create(['name' => 'user1']);
        $user2 = User::create(['name' => 'user2', 'private' => true]);

        $user1->follow($user2);

        $this->assertCount(1, $user1->followings()->get());
        $this->assertCount(0, $user1->approvedFollowings()->get());
        $this->assertCount(1, $user1->notApprovedFollowings()->get());

        $this->assertCount(1, $user2->followers()->get());
        $this->assertCount(0, $user2->approvedFollowers()->get());
        $this->assertCount(1, $user2->notApprovedFollowers()->get());

        $user2->acceptFollowRequestFrom($user1);

        $this->assertCount(1, $user1->followings()->get());
        $this->assertCount(1, $user1->approvedFollowings()->get());
        $this->assertCount(0, $user1->notApprovedFollowings()->get());
        $this->assertCount(1, $user2->followers()->get());
        $this->assertCount(1, $user2->approvedFollowers()->get());
        $this->assertCount(0, $user2->notApprovedFollowers()->get());
    }
}
