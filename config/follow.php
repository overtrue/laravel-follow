<?php

/*
 * This file is part of the overtrue/laravel-follow
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

return [
    /*
     * Model class name of users.
     */
    'user_model' => config('auth.providers.users.model', App\User::class),

    /*
     * Table name of users table.
     */
    'users_table_name' => 'users',

    /*
     * Primary key of users table.
     */
    'users_table_primary_key' => 'id',

    /*
     * Foreign key of users table.
     */
    'users_table_foreign_key' => 'user_id',

    /*
     * Table name of followable relations.
     */
    'followable_table' => 'followables',

    /*
     * Prefix of many-to-many relation fields.
     */
    'morph_prefix' => 'followable',

    /*
     * Date format for created_at.
     */
    'date_format' => 'Y-m-d H:i:s',

    /*
     * Namespace of models.
     */
    'model_namespace' => 'App',
];
