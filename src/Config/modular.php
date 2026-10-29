<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Directorio de Módulos
    |--------------------------------------------------------------------------
    | Aquí se define la ruta relativa o nombre de la carpeta donde residirán
    | los módulos de la aplicación. Por defecto: 'modules' en la raíz.
    */
    'modules_directory' => 'modules',

    /*
    |--------------------------------------------------------------------------
    | Namespace base para los Módulos
    |--------------------------------------------------------------------------
    */
    'modules_namespace' => 'Modules',

    /*
    |--------------------------------------------------------------------------
    | Carga automática de componentes por módulo
    |--------------------------------------------------------------------------
    | Puedes activar o desactivar qué elementos se descubren automáticamente.
    */
    'auto_load' => [
        'routes'     => true,
        'migrations' => true,
        'views'      => true,
    ],
];
