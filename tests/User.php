<?php

namespace Tests;

use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelFollow\Followable;

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
    public function needsToApproveFollowRequests(): bool
    {
        return $this->private ?? false;
    }
}
