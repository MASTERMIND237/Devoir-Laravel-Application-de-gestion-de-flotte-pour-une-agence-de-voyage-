<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    // Autoriser les routes API (inclut /api/satisfy/...)
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

        'allowed_origins' => [
        env('FRONTEND_URL', 'http://localhost:3000'),
        'http://localhost:3000',            // React dev server
        'http://localhost:5173',            // Vite dev server
        'http://127.0.0.1:3000',
        // En production, ajouter : 'https://satisfy-frontend.vercel.app'
    ],

    'allowed_origins_patterns' => [],

        'allowed_headers' => [
        'Content-Type',
        'Authorization',                    // Pour le token Sanctum Bearer
        'Accept',
        'X-Requested-With',
        'X-CSRF-TOKEN',
        'Origin',
    ],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
