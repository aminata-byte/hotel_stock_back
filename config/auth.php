<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | Cette option définit le guard d'authentification par défaut et le broker
    | de réinitialisation de mot de passe pour votre application.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Ici, vous pouvez définir chaque guard d'authentification pour votre application.
    | Une configuration par défaut est fournie utilisant le stockage de session
    | et le provider Eloquent.
    |
    | Supporté : "session", "sanctum"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'api' => [
            'driver' => 'sanctum',
            'provider' => 'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | Tous les guards d'authentification ont un provider qui définit comment
    | les utilisateurs sont récupérés depuis la base de données ou un autre
    | système de stockage. Généralement, Eloquent est utilisé.
    |
    | Supporté : "database", "eloquent"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', App\Models\User::class),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | Ces options spécifient le comportement de la fonctionnalité de réinitialisation
    | de mot de passe, y compris la table utilisée pour stocker les tokens
    | et le provider utilisé pour récupérer les utilisateurs.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Ici, vous pouvez définir le nombre de secondes avant l'expiration de la fenêtre
    | de confirmation de mot de passe. Par défaut, la durée est de trois heures.
    |
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
