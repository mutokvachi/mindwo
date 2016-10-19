<?php

return [    
    /**
     * 
     */
    'host' => env('OPENLDAP_HOST', 'ldaps://test.test.com'),
    
    'port' => env('OPENLDAP_PORT', 636),
    
    /**
     * 
     */
    'root_dn' => env('OPENLDAP_ROOT_DN', 'cn=Manager,dc=9009,dc=in'),
    
    /**
     * 
     */
    'root_password' => env('OPENLDAP_ROOT_PASSWORD', 'P@ssword'),
];