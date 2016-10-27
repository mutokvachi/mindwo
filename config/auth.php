<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */
    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],
    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | here which uses session storage and the Eloquent user provider.
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | Supported: "session", "token"
    |
    */
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'api' => [
            'driver' => 'token',
            'provider' => 'users',
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | If you have multiple user tables or models you may configure multiple
    | sources which represent each model / table. These sources may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */
    'providers' => [
        /*
        'users' => [
            'driver' => 'eloquent',
            'model' => App\User::class,
        ],        
        */
        'users' => [
            'driver' => 'database',
            'table' => 'dx_users',
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | Here you may set the options for resetting passwords including the view
    | that is your password reset e-mail. You may also set the name of the
    | table that maintains all of the reset tokens for your application.
    |
    | You may specify multiple password reset configurations if you have more
    | than one user table or model in the application and you want to have
    | separate password reset settings based on the specific user types.
    |
    | The expire time is the number of minutes that the reset token should be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    */
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'email' => 'auth.emails.password',
            'table' => 'password_resets',
            'expire' => 60,
        ],
    ],
        
    /*
    |--------------------------------------------------------------------------
    | Allowed incorrect password attempt count
    |--------------------------------------------------------------------------
    |
    | Determines how many times it is allowed to input wrong password.
    | If limit is reached then user accoutn is blocked for specified time (parameter "temp_block_minutes")
    | After account is unblocked  Pēc atbloķēšanas atkal ievadot nepareizu paroli, konts tiek neatgriezeniski bloķēts
    |
    */
    
    'try_count' => 3,
    
    /*
    |--------------------------------------------------------------------------
    | Temporary account blocking in minutes
    |--------------------------------------------------------------------------
    |
    | If user exceedes allowed authentication tries, then block account for specified time
    |
    */  
    
    'temp_block_minutes' => 5,

    /**
     * Authentication types 
     * Avaialable options - 'OPENLDAP', 'DEFAULT', 'AD'
     * Each type must be seperated with ';'. Example 'OPENLDAP;DEFAULT'
     * Authentication is attempted in specified order.
     */
    'type' => env('AUTH_TYPE', 'DEFAULT'),
    
    /**
     * If Active Directory or OpenLDAP authentication succeed but user doesn't exist then if this option is true, new user will be created
     */
    'create_user_if_not_exist' => env('AUTH_CREATE_USER_IF_NOT_EXIST', true)
];
