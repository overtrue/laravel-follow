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
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Overtrue\LaravelFollow\Follow;

class FollowTest extends TestCase
{
    public function testIsRelationExists()
    {
        // case 1: with class
        $class = 'App\Channel';
        $target = 12;
        $model = \Mockery::mock(Model::class);
        $model->shouldReceive('followings')->with($class)->andReturnSelf()->once();
        $model->shouldReceive('where')->with('followable_id', $target)->andReturnSelf()->once();
        $model->shouldReceive('exists')->withNoArgs()->andReturn(true)->once();

        $this->assertTrue(Follow::isRelationExists($model, 'followings', $target, $class));

        // case 2: without class
        config(['follow.user_model' => 'App\User']);

        $target = 12;
        $model = \Mockery::mock(Model::class);
        $model->shouldReceive('followings')->with('App\User')->andReturnSelf()->once();
        $model->shouldReceive('where')->with('user_id', $target)->andReturnSelf()->once();
        $model->shouldReceive('exists')->withNoArgs()->andReturn(true)->once();

        $this->assertTrue(Follow::isRelationExists($model, 'followings', $target));
    }

    public function testAttachRelations()
    {
        $morph = \Mockery::mock(MorphToMany::class);
        $morph->shouldReceive('getRelationName')->andReturn('followings');

        $targets = [1, 2];
        $class = 'App\User';
        $model = \Mockery::mock(Model::class);
        $model->shouldReceive('followings')->withNoArgs()->andReturn($morph)->once();
        $model->shouldReceive('followings')->with('App\User')->andReturnSelf()->once();
        $model->shouldReceive('sync')->with(\Mockery::type('array'), false)->once()->andReturn([1, 2, 3]);

        $this->assertSame([1, 2, 3], Follow::attachRelations($model, 'followings', $targets, $class));
    }

    public function testAttachRelationsWithoutInvalidRelationDefinition()
    {
        $morph = \Mockery::mock(MorphToMany::class);
        $morph->shouldReceive('getRelationName')->andReturn('undefined');

        $targets = [1, 2];
        $class = 'App\User';
        $model = \Mockery::mock(Model::class);
        $model->shouldReceive('followings')->withNoArgs()->andReturn($morph)->once();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid relation definition.');

        Follow::attachRelations($model, 'followings', $targets, $class);
    }

    public function testDetachRelations()
    {
        $targets = [1, 2];
        $class = 'App\Foo';
        $model = \Mockery::mock(Model::class);
        $model->shouldReceive('followings')->with($class)->andReturnSelf()->once();
        $model->shouldReceive('detach')->with($targets)->andReturn([1, 3])->once();

        $this->assertSame([1, 3], Follow::detachRelations($model, 'followings', $targets, $class));
    }

    public function testToggleRelations()
    {
        $targets = [1, 2];
        $class = 'App\Foo';
        $morph = \Mockery::mock(MorphToMany::class);
        $morph->shouldReceive('getRelationName')->andReturn('followings');

        $model = \Mockery::mock(Model::class);
        $model->shouldReceive('followings')->with()->andReturn($morph)->once();
        $model->shouldReceive('followings')->with($class)->andReturnSelf()->once();
        $model->shouldReceive('toggle')->with(\Mockery::type('array'))->andReturn([1, 3])->once();

        $this->assertSame([1, 3], Follow::toggleRelations($model, 'followings', $targets, $class));
    }

    public function testAttachPivotsFromRelation()
    {
        $morph = \Mockery::mock(MorphToMany::class);
        $morph->shouldReceive('getRelationName')->andReturn('followings');

        $targets = Follow::attachPivotsFromRelation($morph, [1, 34], 'App\Foo');

        $this->assertArrayHasKey(1, $targets->targets);
        $this->assertArrayHasKey(34, $targets->targets);
        $this->assertSame('follow', $targets->targets[1]['relation']);
        $this->assertSame('follow', $targets->targets[34]['relation']);
        $this->assertStringStartsWith(date('Y-m-d H:i:'), $targets->targets[34]['created_at']);
        $this->assertRegExp('/^\d{4}(\-\d{2}){2} (\d{2}:){2}\d{2}$/', $targets->targets[34]['created_at']);
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
