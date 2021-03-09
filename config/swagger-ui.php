<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Swagger UI - Path
    |--------------------------------------------------------------------------
    |
    | This is the URI path where SwaggerUI will be accessible from. Feel free
    | to change this path to anything you like.
    |
    */

    'path' => 'docs',

    /*
    |--------------------------------------------------------------------------
    | Swagger UI - OpenAPI File
    |--------------------------------------------------------------------------
    |
    | This is the location of the project's OpenAPI / Swagger JSON file. It's
    | this file that will be used in Swagger UI.
    |
    */

    'file' => base_path('swagger.json'),
];
