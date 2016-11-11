<?php
namespace Overtrue\LaravelFollow\Test;

use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelFollow\FollowTrait;

class UserTest extends Model
{
    use FollowTrait;

    protected $table = 'users';

    protected $fillable = ['name'];
}