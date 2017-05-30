<?php

return [
    /**
     * Model class name of users.
     */
    'user_model' => 'App\User',

    /**
     * Table name of users table.
     */
    'users_table_name' => 'users',

    /**
     * Primary key of users table.
     */
    'users_table_primary_key' => 'id',

    /**
     * Table name of followable relations.
     */
    'followable_table' => 'followables',

    /**
     * Prefix of many-to-many relation fields.
     */
    'morph_prefix' => 'followable',

    /**
     * Date format for created_at.
     */
    'date_format' => 'Y-m-d H:i:s',

    /**
     * Namespace of models.
     */
    'model_namespace' => 'App',
];