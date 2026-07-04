<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    |
    | The complete list of permissions available within an organisation. These
    | are created globally (guard scoped) and assigned to organisation-scoped
    | roles via the matrix below.
    |
    */

    'permissions' => [
        'view organisation',
        'update organisation',
        'delete organisation',
        'manage members',
        'manage roles',
        'view payments',
        'record payments',
        'view reports',
    ],

    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    |
    | The default roles provisioned for every new organisation, mapped to the
    | permissions each role is granted. Use the wildcard '*' to grant every
    | permission. Roles are scoped per organisation (spatie teams feature).
    |
    */

    'roles' => [
        'Owner' => ['*'],

        'Admin' => [
            'view organisation',
            'update organisation',
            'manage members',
            'manage roles',
            'view payments',
            'record payments',
            'view reports',
        ],

        'Manager' => [
            'view organisation',
            'manage members',
            'view payments',
            'record payments',
            'view reports',
        ],

        'Agent' => [
            'view organisation',
            'view payments',
            'record payments',
        ],

        'Viewer' => [
            'view organisation',
            'view payments',
            'view reports',
        ],
    ],

];
