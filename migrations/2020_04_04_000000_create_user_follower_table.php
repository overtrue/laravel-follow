<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFollowerTable extends Migration
{
    public function up()
    {
        Schema::create(config('follow.relation_table', 'user_follower'), function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('following_id')->index();
            $table->unsignedBigInteger('follower_id')->index();
            $table->timestamp('accepted_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(config('follow.relation_table', 'user_follower'));
    }
}
