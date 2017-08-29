<?php

/**
 * Contains configs for education module functionality
 */
return [
    
    /*
    |--------------------------------------------------------------------------
    | Education module users roles
    |--------------------------------------------------------------------------
    |
    | Array with roles IDs from table dx_roles
    */
    
    'roles' => [
        'main_coord' => env('EDU_ROLE_MAIN', 74), // main coordinator (full rights)
        'org_coord' => env('EDU_ROLE_ORG', 75), // organization coordinator
        'teacher' => env('EDU_ROLE_TEACHER', 76),
        'student' => env('EDU_ROLE_STUDENT', 77),
        'support' => env('EDU_ROLE_SUPPORT', 78),
    ],

    /*
    |--------------------------------------------------------------------------
    | Education module employees loading results limit
    |--------------------------------------------------------------------------
    |
    | UI have 2 modes:
    |   1. for small organizations will be loaded all employees and search will be done via jQuery
    |   2. for big organizations will be loaded top employees and search will be done via AJAX in db
    |
    | So, this parameter indicates what is "small" or "big" organization. Not recommended to set this value more than 1000 because of performance issues
    */
    'empl_load_limit' => env('EDU_EMPL_LOAD_LIMIT', 300),
    
];