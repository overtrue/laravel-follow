<?php

/*
 * This file is part of the overtrue/laravel-follow
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLaravelFollowTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create(config('follow.followable_table', 'followables'), function (Blueprint $table) {
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('followable_id');
            $table->string('followable_type')->index();
            $table->string('relation')->default('follow')->comment('folllow/like/subscribe/favorite/');
            $table->timestamp('created_at');

            $table->foreign('user_id')
                ->references(config('follow.users_table_primary_key', 'id'))
                ->on(config('follow.users_table_name', 'users'))
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table(config('follow.followable_table', 'followables'), function ($table) {
            $table->dropForeign(config('follow.followable_table', 'followables').'_user_id_foreign');
        });

        Schema::drop(config('follow.followable_table', 'followables'));
    }
}
