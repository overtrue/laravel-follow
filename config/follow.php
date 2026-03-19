<?php

use Overtrue\LaravelFollow\Followable;

return [
    /*
     * Use uuid as primary key.
     */
    'uuids' => false,

    /*
     * User tables foreign key name.
     */
    'user_foreign_key' => 'user_id',

    /*
     * Table name for followers table.
     */
    'followables_table' => 'followables',

    /**
     * Model class name for followers table.
     */
    'followables_model' => Followable::class,
];
