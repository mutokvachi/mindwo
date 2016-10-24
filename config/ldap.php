<?php

/**
 * Contains configuration data for connecting and using Open LDAP
 */
return [    
    /**
     * Account suffix which is added to login when authenticating
     */
    'account_suffix' => env('AD_ACCOUNT_SUFFIX', ''),
    
    /**
     * Active directory domain controller
     */      
    'domain_controller' => env('AD_DOMAIN_CONTROLLER', 'whitepages.gatech.edu'),
    
    /**
     * Root DN
     */     
    'base_dn' => env('AD_BASE_DN', 'dc=whitepages,dc=gatech,dc=edu'),
    
    /**
     * Active Directory's admin's user name
     */
    'admin_username' => env('AD_ADMIN_USERNAME', ''),
    
    /**
     * Active Directory's admin's password
     */     
    'admin_password' => env('AD_ADMIN_PASSWORD', ''),
];