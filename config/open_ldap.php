<?php

/**
 * Contains configuration data for connecting and using Open LDAP
 */
return [    
    /**
     * Open LDAP host address
     */
    'host' => env('OPENLDAP_HOST', 'ldaps://test.test.com'),
    
    /**
     * Oppen LDAP connection port
     */
    'port' => env('OPENLDAP_PORT', 636),
    
    /**
     * Accoutn prefix which is added in the front of given user name. This is used when authenticating user.
     */
    'account_prefix' => env('OPENLDAP_ACCOUNT_PREFIX', 'mail='),
    
    /**
     * Accoutn suffix which is added in the end of given user name. This is used when authenticating user. 
     */
    'account_suffix' => env('OPENLDAP_ACCOUNT_SUFFIX', ',ou=Users,domainName=test.com,o=domains,dc=9009,dc=in'),
    
    /**
     * Root DN
     */
    'root_dn' => env('OPENLDAP_ROOT_DN', 'cn=Manager,dc=9009,dc=in'),
    
    /**
     * Password for accessing root DN
     */
    'root_password' => env('OPENLDAP_ROOT_PASSWORD', 'P@ssword'),
];