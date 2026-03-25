<?php

use Laravel\Sanctum\Sanctum;

return [

    /*
    |--------------------------------------------------------------------------
    | Stateful Domains
    |--------------------------------------------------------------------------
    | Domaines autorisés à utiliser les cookies de session Sanctum.
    | Pour notre projet : le frontend (Vercel) + localhost en développement.
    |
    | En production, remplacer par le vrai domaine du frontend.
    */

    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', implode(',', [
        'localhost',
        'localhost:3000',       // React dev server
        'localhost:5173',       // Vite dev server
        'localhost:8000',       // Laravel dev server
        '127.0.0.1',
        '127.0.0.1:3000',
        parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST),
        parse_url(env('FRONTEND_URL', 'http://localhost:3000'), PHP_URL_HOST),
    ]))),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Guards
    |--------------------------------------------------------------------------
    */

    'guard' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Expiration des tokens (en minutes)
    |--------------------------------------------------------------------------
    | null = jamais expirer
    | 10080 = 7 jours
    | 43200 = 30 jours
    |
    | On met 30 jours pour le confort des chauffeurs sur la PWA mobile.
    */

    'expiration' => 43200,                  // 30 jours

    /*
    |--------------------------------------------------------------------------
    | Token Prefix
    |--------------------------------------------------------------------------
    | Préfixe ajouté aux tokens pour les identifier facilement dans les logs.
    */

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', 'satisfy_'),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Middleware
    |--------------------------------------------------------------------------
    */

    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'encrypt_cookies'      => Illuminate\Cookie\Middleware\EncryptCookies::class,
        'validate_csrf_token'  => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ],

];