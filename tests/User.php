<?php

/*
 * This file is part of the overtrue/laravel-followable.
 *
 * (c) overtrue <anzhengchao@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Tests;

use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelFollow\Followable;

/**
 * Class User.
 */
class User extends Model
{
    use Followable;

    protected $fillable = ['name'];
}
