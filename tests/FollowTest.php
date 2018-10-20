<?php

/*
 * This file is part of the overtrue/laravel-follow
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\LaravelFollow\Test;

use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelFollow\Follow;

class FollowTest extends TestCase
{
    public function testIsRelationExists()
    {
        $user = User::create(['name' => 'overtrue']);
        $other = Other::create(['name' => 'php']);

        $user->follow($other);

        $this->assertTrue(Follow::isRelationExists($user, 'followings', $other->id, \get_class($other)));

        $user1 = User::create(['name' => 'overtrue']);
        $user2 = User::create(['name' => 'anzhengchao']);
        $user1->follow($user2);

        $this->assertTrue(Follow::isRelationExists($user1, 'followings', $user2->id, User::class));
    }

    public function testAttachAndDetachRelations()
    {
        $user1 = User::create(['name' => 'overtrue']);
        $user2 = User::create(['name' => 'anzhengchao']);
        $user3 = User::create(['name' => 'allen']);
        $user4 = User::create(['name' => 'taylor']);
        $user1->follow($user2);
        $user1->follow([$user3, $user4]);

        $this->assertTrue(Follow::isRelationExists($user1, 'followings', $user2->id, User::class));
        $this->assertTrue(Follow::isRelationExists($user1, 'followings', $user3->id, User::class));
        $this->assertTrue(Follow::isRelationExists($user1, 'followings', $user4->id, User::class));

        $user1->unfollow($user2);
        $this->assertFalse(Follow::isRelationExists($user1, 'followings', $user2->id, User::class));
    }

    public function testToggleRelations()
    {
        $user1 = User::create(['name' => 'overtrue']);
        $user2 = User::create(['name' => 'anzhengchao']);
        $user1->follow($user2);

        $this->assertTrue(Follow::isRelationExists($user1, 'followings', $user2->id, User::class));

        $user1->toggleFollow($user2);
        $this->assertFalse(Follow::isRelationExists($user1, 'followings', $user2->id, User::class));

        $user1->toggleFollow($user2);
        $this->assertTrue(Follow::isRelationExists($user1, 'followings', $user2->id, User::class));
    }

    public function testEagerLoading()
    {
        $sqls = \collect([]);

        $user1 = User::create(['name' => 'overtrue']);
        $user2 = User::create(['name' => 'anzhengchao']);
        $user3 = User::create(['name' => 'allen']);
        $user4 = User::create(['name' => 'taylor']);
        $user1->follow($user2);
        $user1->follow([$user3, $user4]);

        // start recording
        \DB::listen(function ($query) use ($sqls) {
            $sqls->push($query->sql);
        });

        $user1->isFollowing($user2);
        $user1->isFollowing($user3);
        $user1->isFollowing($user4);

        $this->assertCount(3, $sqls);

        // eager loading
        $user1->load('followings');

        // cleanup
        $sqls = \collect([]);

        $user1->isFollowing($user2);
        $user1->isFollowing($user3);
        $user1->isFollowing($user4);

        $this->assertCount(0, $sqls);
    }

    public function testFormatTargets()
    {
        // 1. !is_array
        $result = Follow::formatTargets(1, 'App\Foo');
        $this->assertSame('App\Foo', $result->classname);
        $this->assertSame([1], $result->ids);
        $this->assertSame([1], $result->targets);

        // 2. Model
        $user = new User();
        $user->id = 3;
        $result = Follow::formatTargets([1, $user], 'App\Foo');
        $this->assertSame(User::class, $result->classname);
        $this->assertSame([1, 3], $result->ids);
        $this->assertSame([1, 3], $result->targets);

        $other = new Other();
        $other->id = 45;

        $result = Follow::formatTargets([1, $user, $other], 'App\Foo');
        $this->assertSame(Other::class, $result->classname);
        $this->assertSame([1, 3, 45], $result->ids);
        $this->assertSame([1, 3, 45], $result->targets);

        // 3. $update
        $update = ['relation' => 'like'];
        $result = Follow::formatTargets([1, 2], 'App\Foo', $update);
        $this->assertSame('App\Foo', $result->classname);
        $this->assertSame([1, 2], $result->ids);
        $this->assertSame([1 => $update, 2 => $update], $result->targets);
    }
}
