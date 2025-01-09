<?php
return [

    /*
    |----------------------------------------------------------------------
    | Authentication Defaults
    |----------------------------------------------------------------------
    |
    | This option defines the default authentication "guard" and password
    | reset "broker" for your application. You may change these values
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

    /*
    |----------------------------------------------------------------------
    | Authentication Guards
    |----------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | All authentication guards have a user provider, which defines how the
    | users are actually retrieved from your database or other storage.
    |
    | Supported: "session"
    |
    */

   'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    'admin' => [
        'driver' => 'session',
        'provider' => 'admins',  // Sử dụng 'admins' provider cho admin guard
    ],
],

    /*
    |----------------------------------------------------------------------
    | User Providers
    |----------------------------------------------------------------------
    |
    | All authentication guards have a user provider, which defines how the
    | users are actually retrieved from your database or other storage.
    |
    | Supported: "eloquent", "database"
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => env('AUTH_MODEL', App\Models\User::class),
        ],

        'admins' => [
            'driver' => 'eloquent',
            'model' => App\Models\Admin::class,  // Model Admin cho guard admin
        ],
    ],

    /*
    |----------------------------------------------------------------------
    | Password Reset Configuration
    |----------------------------------------------------------------------
    |
    | These configuration options specify the behavior of Laravel's password
    | reset functionality, including the table used for token storage and
    | the user provider that retrieves users.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
        
        // Nếu bạn muốn cấu hình reset mật khẩu cho admin:
        'admins' => [
            'provider' => 'admins',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'admin_password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |----------------------------------------------------------------------
    | Password Confirmation Timeout
    |----------------------------------------------------------------------
    |
    | Define the timeout for password confirmation requests.
    |
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
