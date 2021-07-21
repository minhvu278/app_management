<?php

return [
    [
        'name' => 'App managements',
        'flag' => 'app-management.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'app-management.create',
        'parent_flag' => 'app-management.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'app-management.edit',
        'parent_flag' => 'app-management.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'app-management.destroy',
        'parent_flag' => 'app-management.index',
    ],
    [
        'name' => 'Apps',
        'flag' => 'app.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'app.create',
        'parent_flag' => 'app.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'app.edit',
        'parent_flag' => 'app.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'app.destroy',
        'parent_flag' => 'app.index',
    ],
    [
        'name' => 'App versions',     
        'flag' => 'app-version.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'app-version.create',
        'parent_flag' => 'app-version.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'app-version.edit',
        'parent_flag' => 'app-version.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'app-version.destroy',
        'parent_flag' => 'app-version.index',
    ],
];
