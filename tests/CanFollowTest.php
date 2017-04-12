<?php
namespace Overtrue\LaravelFollow\Test;

use Illuminate\Database\Eloquent\ModelNotFoundException;

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

    public function test_follow_not_existing_user()
    {
        $user1 = User::find(1);
        $user2 = User::find(4);

        try {
            $user1->follow($user2);
        } catch (\Exception $e) {
            $this->assertInstanceOf('Illuminate\Database\Eloquent\ModelNotFoundException', $e);
        }
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

    public function test_follow_not_existing_other()
    {
        $user = User::find(1);
        $other = Other::find(4);

        try {
            $user->follow($other);
        } catch (\Exception $e) {
            $this->assertInstanceOf('Illuminate\Database\Eloquent\ModelNotFoundException', $e);
        }
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
}