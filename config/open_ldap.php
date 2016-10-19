<?php

return [    
    /**
     * 
     */
    'host' => env('OPENLDAP_HOST', 'ldaps://test.test.com'),
    
    'port' => env('OPENLDAP_PORT', 636),
    
    'account_prefix' => env('OPENLDAP_ACCOUNT_PREFIX', 'mail='),
    
    'account_suffix' => env('OPENLDAP_ACCOUNT_SUFFIX', ',ou=Users,domainName=test.com,o=domains,dc=9009,dc=in'),
    
    /**
     * 
     */
    'root_dn' => env('OPENLDAP_ROOT_DN', 'cn=Manager,dc=9009,dc=in'),
    
    /**
     * 
     */
    'root_password' => env('OPENLDAP_ROOT_PASSWORD', 'P@ssword'),
];