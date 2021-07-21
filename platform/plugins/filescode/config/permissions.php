<?php

return [
    [
        'name' => 'Filescodes',
        'flag' => 'filescode.index',
    ],
    [
        'name'        => 'Create',
        'flag'        => 'filescode.create',
        'parent_flag' => 'filescode.index',
    ],
    [
        'name'        => 'Edit',
        'flag'        => 'filescode.edit',
        'parent_flag' => 'filescode.index',
    ],
    [
        'name'        => 'Delete',
        'flag'        => 'filescode.destroy',
        'parent_flag' => 'filescode.index',
    ],
];
