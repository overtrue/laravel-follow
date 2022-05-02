<?php

namespace Tests;

use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelFollow\Traits\Followable;
use Overtrue\LaravelFollow\Traits\Follower;

class User extends Model
{
    use Followable;
    use Follower;

    protected $fillable = ['name', 'private'];

    protected $casts = [
        'private' => 'boolean',
    ];

    /**
     * @return bool
     */
    public function needsToApproveFollowRequests(): bool
    {
        return $this->private ?? false;
    }
}
