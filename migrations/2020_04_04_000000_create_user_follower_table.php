<?php

/*
 * This file is part of the overtrue/laravel-followable.
 *
 * (c) overtrue <anzhengchao@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFollowerTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create(config('follow.relation_table', 'user_follower'), function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('following_id')->index();
            $table->unsignedBigInteger('follower_id')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists(config('follow.followings_table'));
    }
}
