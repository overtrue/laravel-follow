<?php
namespace Overtrue\LaravelFollow\Test;

use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelFollow\FollowerTrait;

class Other extends Model
{
    use FollowerTrait;

    protected $follow = User::class;

    protected $table = 'others';

    protected $fillable = ['name'];
}