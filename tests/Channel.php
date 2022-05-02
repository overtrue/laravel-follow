<?php

namespace Tests;

use Illuminate\Database\Eloquent\Model;
use Overtrue\LaravelFollow\Traits\Followable;

class Channel extends Model
{
    use Followable;

    protected $fillable = ['name'];
}
