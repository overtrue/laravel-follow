<?php
namespace Overtrue\LaravelFollow\Test;

use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelFollow\Traits\CanFollow;
use Overtrue\LaravelFollow\Traits\CanBeFollowed;

class User extends Model
{
    use CanFollow, CanBeFollowed;

    protected $table = 'users';

    protected $fillable = ['name'];
}