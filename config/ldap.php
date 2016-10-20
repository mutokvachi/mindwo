<?php

/**
 * Contains configuration data for connecting and using Open LDAP
 */
return [    
    /**
     * Account suffix which is added to login when authenticating
     */
    'account_suffix' => '@gatech.edu',
    
    /**
     * Active directory domain controller
     */      
    'domain_controller' => 'whitepages.gatech.edu',
    
    /**
     * Root DN
     */     
    'base_dn' => 'dc=whitepages,dc=gatech,dc=edu',
    
    /**
     * Active Directory's admin's user name
     */
    'admin_username' => '',
    
    /**
     * Active Directory's admin's password
     */     
    'admin_password' => '',
];