<?php

namespace Tests;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Overtrue\LaravelFollow\Events\Followed;
use Overtrue\LaravelFollow\Events\Unfollowed;

class FeatureTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Event::fake();

        config(['auth.providers.users.model' => User::class]);
    }

    public function test_user_can_follow_users()
    {
        $user1 = User::create(['name' => 'user1']);
        $user2 = User::create(['name' => 'user2']);

        $user1->follow($user2);

        Event::assertDispatched(
            Followed::class,
            function ($event) use ($user1, $user2) {
                return $event->followable_type === $user2->getMorphClass()
                    && $event->followable_id === $user2->id
                    && $event->user_id === $user1->id;
            }
        );

        $this->assertTrue($user1->isFollowing($user2));
        $this->assertTrue($user2->isFollowedBy($user1));

        $user1->unfollow($user2);

        Event::assertDispatched(
            Unfollowed::class,
            function ($event) use ($user1, $user2) {
                return $event->followable_type === $user2->getMorphClass()
                    && $event->followable_id === $user2->id
                    && $event->user_id === $user1->id;
            }
        );
    }

    public function test_user_can_follow_channels()
    {
        $user = User::create(['name' => 'user1']);
        $channel = Channel::create(['name' => 'Laravel']);

        $user->follow($channel);

        Event::assertDispatched(
            Followed::class,
            function ($event) use ($user, $channel) {
                return $event->followable_type === $channel->getMorphClass()
                    && $event->followable_id === $channel->id
                    && $event->user_id === $user->id;
            }
        );

        $this->assertTrue($user->isFollowing($channel));
        $this->assertTrue($channel->isFollowedBy($user));

        $user->unfollow($channel);

        Event::assertDispatched(
            Unfollowed::class,
            function ($event) use ($user, $channel) {
                return $event->followable_type === $channel->getMorphClass()
                    && $event->followable_id === $channel->id
                    && $event->user_id === $user->id;
            }
        );
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

    public function test_user_can_get_unfollowed_users()
    {
        $user1 = User::create(['name' => 'user1']);
        $user2 = User::create(['name' => 'user2']);
        $user3 = User::create(['name' => 'user3']);
        $user4 = User::create(['name' => 'user4']);

        $user1->follow($user4);
        $user1UnfollowedUsers = User::whereNotIn(
            'id',
            function ($q) use ($user1) {
                $q->select('followable_id')->from('followables')->where('user_id', $user1->id);
            }
        )->where('id', '<>', $user1->id)->get()->toArray();
        $this->assertCount(2, $user1UnfollowedUsers);
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
        $sqls = $this->getQueryLog(
            function () use ($user1, $user2, $user3, $user4) {
                $user1->isFollowing($user2);
                $user1->isFollowing($user3);
                $user1->isFollowing($user4);
            }
        );

        $this->assertSame(3, $sqls->count());

        // -- following
        // with eager loading
        $user1->load('followings');
        $sqls = $this->getQueryLog(
            function () use ($user1, $user2, $user3, $user4) {
                $user1->isFollowing($user2);
                $user1->isFollowing($user3);
                $user1->isFollowing($user4);
            }
        );
        $this->assertSame(0, $sqls->count());

        // -- followers
        // without eager loading
        $sqls = $this->getQueryLog(
            function () use ($user1, $user2, $user3, $user4) {
                $user4->isFollowedBy($user1);
                $user4->isFollowedBy($user2);
                $user4->isFollowedBy($user3);
            }
        );

        $this->assertSame(3, $sqls->count());

        // with eager loading
        $user4->load('followables');
        $sqls = $this->getQueryLog(
            function () use ($user1, $user2, $user3, $user4) {
                $user4->isFollowedBy($user1);
                $user4->isFollowedBy($user2);
                $user4->isFollowedBy($user3);
            }
        );
        $this->assertSame(0, $sqls->count());
    }

    /**
     * @param  \Closure  $callback
     *
     * @return \Illuminate\Support\Collection
     */
    protected function getQueryLog(\Closure $callback): \Illuminate\Support\Collection
    {
        $sqls = \collect([]);
        \DB::listen(
            function ($query) use ($sqls) {
                $sqls->push(['sql' => $query->sql, 'bindings' => $query->bindings]);
            }
        );

        $callback();

        return $sqls;
    }

    public function test_attach_follow_status()
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

        $users = User::all();

        $sqls = $this->getQueryLog(
            function () use ($user1, $users) {
                $user1->attachFollowStatus($users);
            }
        );

        $this->assertSame(1, $sqls->count());

        $this->assertNull($users[0]->followed_at);
        $this->assertNotNull($users[1]->followed_at);

        $this->assertInstanceOf(Carbon::class, $users[1]->followed_at);

        $this->assertNotNull($users[2]->followed_at);
        $this->assertNotNull($users[3]->followed_at);

        // paginator
        $users = User::paginate();
        $user1->attachFollowStatus($users);

        $users = $users->toArray()['data'];
        $this->assertNull($users[0]['followed_at']);
        $this->assertNotNull($users[1]['followed_at']);
        $this->assertNotNull($users[2]['followed_at']);
        $this->assertNotNull($users[3]['followed_at']);

        // cursor paginator
        $users = User::cursorPaginate();
        $user1->attachFollowStatus($users);

        $users = $users->toArray()['data'];
        $this->assertNull($users[0]['followed_at']);
        $this->assertNotNull($users[1]['followed_at']);
        $this->assertNotNull($users[2]['followed_at']);
        $this->assertNotNull($users[3]['followed_at']);

        // cursor
        $users = User::cursor();
        $users = $user1->attachFollowStatus($users)->toArray();
        $this->assertNull($users[0]['followed_at']);
        $this->assertNotNull($users[1]['followed_at']);
        $this->assertNotNull($users[2]['followed_at']);
        $this->assertNotNull($users[3]['followed_at']);

        // with custom resolver
        $users = \collect(['creator' => $user2], ['creator' => $user3], ['creator' => $user4]);
        $user1->attachFollowStatus($users, fn ($post) => $post['creator']);
    }

    public function test_order_by_followers()
    {
        /* @var \Tests\User $user1 */
        /* @var \Tests\User $user2 */
        /* @var \Tests\User $user3 */
        /* @var \Tests\User $user4 */
        /* @var \Tests\User $user5 */
        $user1 = User::create(['name' => 'user1']);
        $user2 = User::create(['name' => 'user2']);
        $user3 = User::create(['name' => 'user3']);
        $user4 = User::create(['name' => 'user4']);
        $user5 = User::create(['name' => 'user5']);

        // user2: 2 followers
        $user1->follow($user2);
        $user3->follow($user2);

        // user3: 0 followers
        // user4: 1 followers
        $user1->follow($user4);

        // user1: 3 followers
        $user2->follow($user1);
        $user3->follow($user1);
        $user4->follow($user1);

        $usersOrderByFollowersCount = User::orderByFollowersCountDesc()->get();
        // same as:
        // $usersOrderByFollowersCount = User::withCount('followers')->orderByDesc('followers_count')->get();

        $this->assertSame($user1->name, $usersOrderByFollowersCount[0]->name);
        $this->assertEquals(3, $usersOrderByFollowersCount[0]->followers_count);
        $this->assertSame($user2->name, $usersOrderByFollowersCount[1]->name);
        $this->assertEquals(2, $usersOrderByFollowersCount[1]->followers_count);
        $this->assertSame($user4->name, $usersOrderByFollowersCount[2]->name);
        $this->assertEquals(1, $usersOrderByFollowersCount[2]->followers_count);
        $this->assertSame($user3->name, $usersOrderByFollowersCount[3]->name);
        $this->assertEquals(0, $usersOrderByFollowersCount[3]->followers_count);

        $mostPopularUser = User::orderByFollowersCountDesc()->first();
        // same as:
        // $mostPopularUser = Post::withCount('followables')->orderByDesc('followers_count')->first();
        $this->assertSame($user1->name, $mostPopularUser->name);
        $this->assertEquals(3, $mostPopularUser->followers_count);
    }

    public function test_repeat_actions()
    {
        $user1 = User::create(['name' => 'user1']);
        $user2 = User::create(['name' => 'user2']);
        $user3 = User::create(['name' => 'user2']);
        $user4 = User::create(['name' => 'user2']);

        $user1->follow($user2);
        $user1->follow($user2);
        $user1->follow($user2);
        $user1->follow($user3);
        $user1->follow($user4);

        $this->assertDatabaseHas('followables', ['user_id' => $user1->id, 'followable_id' => $user2->id, 'followable_type' => $user2->getMorphClass()]);
        $this->assertDatabaseHas('followables', ['user_id' => $user1->id, 'followable_id' => $user3->id, 'followable_type' => $user3->getMorphClass()]);
        $this->assertDatabaseHas('followables', ['user_id' => $user1->id, 'followable_id' => $user4->id, 'followable_type' => $user4->getMorphClass()]);

        $this->assertDatabaseCount('followables', 3);
    }
}
