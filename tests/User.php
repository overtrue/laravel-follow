<?php
namespace Overtrue\LaravelFollow\Test;

use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelFollow\FollowTrait;
use Overtrue\LaravelFollow\FollowerTrait;

class User extends Model
{
    use FollowTrait, FollowerTrait;

    protected $table = 'users';

    protected $fillable = ['name'];
}