<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Modules Directory
    |--------------------------------------------------------------------------
    | Define the base directory where your application modules reside.
    | Default: 'modules' in the project root.
    */
    'modules_directory' => 'modules',

    /*
    |--------------------------------------------------------------------------
    | Base Modules Namespace
    |--------------------------------------------------------------------------
    */
    'modules_namespace' => 'Modules',

    /*
    |--------------------------------------------------------------------------
    | Component Auto-Discovery
    |--------------------------------------------------------------------------
    | Enable or disable auto-registration for specific module components.
    */
    'auto_load' => [
        'routes'     => true,
        'migrations' => true,
        'views'      => true,
    ],
];
