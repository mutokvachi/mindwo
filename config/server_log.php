<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | Indicates if server access log proceessing is turned on
    |
    | In order to use this functionality there is needed to set up additional server software
    | which will monitor and log all sessions opening/closing on server.
    | 
    | This functionality works only on Linux servers
    |--------------------------------------------------------------------------
    */
    'is_server_audit_on' => env('APP_SERVER_AUDIT_ON', false),
    
    /*
    |--------------------------------------------------------------------------
    | Full path to server access log file
    |--------------------------------------------------------------------------
    */
    'logFilePath' => env('APP_SERVER_AUDIT_LOG_PATH', 'C:\tmp\log\auth.log'),
    
    /*
    |--------------------------------------------------------------------------
    | Full path to directory with write permissions where log file will be copied for data extraction
    |--------------------------------------------------------------------------
    */
    'tempFolderPath' => env('APP_SERVER_AUDIT_TMP_DIR', 'C:\tmp\log\temp\\'),
    
];