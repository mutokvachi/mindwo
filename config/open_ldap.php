<?php

/**
 * Contains configuration data for connecting and using OpenLDAP
 */
return [    
    /**
     * OpenLDAP host address
     */
    'host' => env('OPENLDAP_HOST', 'ldaps://example.com'),
    
    /**
     * OppenLDAP connection port
     */
    'port' => env('OPENLDAP_PORT', 636),
    
    /**
     * OpenLDAP's DN which is used to search for users. This is used when authenticating user. 
     */
    'search_dn' => env('OPENLDAP_SEARCH_DN', 'ou=Users,domainName=example.com,o=domains,dc=9009,dc=in'),
    
    /**
     * OpenLDAP's filter which is used when system tries to find user's data (DN + information) for authentication
     */
    'search_filter' => env('OPENLDAP_SEARCH_FILTER', '(accountStatus=active)'),
    
    /**
     * Root DN
     */
    'root_dn' => env('OPENLDAP_ROOT_DN', 'cn=Manager,dc=9009,dc=in'),
    
    /**
     * Password for accessing root DN
     */
    'root_password' => env('OPENLDAP_ROOT_PASSWORD', 'P@ssword'),
];