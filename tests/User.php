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

use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelFollow\Followable;

/**
 * Class User.
 */
class User extends Model
{
    use Followable;

    protected $fillable = ['name', 'private'];

    protected $casts = [
        'private' => 'boolean',
    ];

    /**
     * @return bool
     */
    public function needsToApproveFollowRequests()
    {
        return $this->private ?? false;
    }
}
