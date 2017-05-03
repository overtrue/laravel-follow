<?php
/*
 * This file is part of the sora.
 *
 * (c) 2016 weibo.com
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
        $builder = new \stdClass();
        $relationType = 'follow';
        $builder->wheres = [
            [
                'column' => 'age',
                'value' => 18,
            ],
            [
                'column' => 'followables.relation',
                'value' => $relationType,
            ],
        ];
        $morph = \Mockery::mock(MorphToMany::class);
        $morph->shouldReceive('getQuery->getQuery')->andReturn($builder);

        $targets = [1, 2];
        $class = 'App\User';
        $model = \Mockery::mock(Model::class);
        $model->shouldReceive('followings')->withNoArgs()->andReturn($morph)->once();
        $model->shouldReceive('followings')->with('App\User')->andReturnSelf()->once();
        $model->shouldReceive('sync')->with(\Mockery::on(function($object) use ($targets, $relationType) {
            return array_keys($object) === $targets
                && $object[$targets[0]]['relation'] === $relationType
                && $object[$targets[1]]['relation'] === $relationType
                && strpos($object[$targets[0]]['created_at'], date('Y-m-d H:i:')) === 0
                && strlen($object[$targets[0]]['created_at']) === 19;
        }), false)->once()->andReturn([1, 2, 3]);

        $this->assertSame([1, 2, 3], Follow::attachRelations($model, 'followings', $targets, $class));
    }

    public function testAttachRelationsWithoutInvalidRelationDefinition()
    {
        $builder = new \stdClass();
        $relationType = 'follow';
        $builder->wheres = [
            [
                'column' => 'age',
                'value' => 18,
            ],
            [
                'column' => 'followables.relation',
                'value' => '',
            ],
        ];
        $morph = \Mockery::mock(MorphToMany::class);
        $morph->shouldReceive('getQuery->getQuery')->andReturn($builder);

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
        $model = \Mockery::mock(Model::class);
        $model->shouldReceive('followings')->with($class)->andReturnSelf()->once();
        $model->shouldReceive('toggle')->with($targets)->andReturn([1, 3])->once();

        $this->assertSame([1, 3], Follow::toggleRelations($model, 'followings', $targets, $class));
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
