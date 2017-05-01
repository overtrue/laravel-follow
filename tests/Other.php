<?php
namespace Overtrue\LaravelFollow\Test;

use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelFollow\Traits\CanBeFollowed;

class Other extends Model
{
    use CanBeFollowed;

    protected $follow = User::class;

    protected $table = 'others';

    protected $fillable = ['name'];
}