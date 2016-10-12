<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | Aktīvās direktorijas (LDAP) konfigurācija
    |--------------------------------------------------------------------------
    |
    | Parametrs ieslēdz/izslēdz LDAP autorizācijas mehānismu
    */
    
    'use_ldap_auth' => false,
    
    /*
    |--------------------------------------------------------------------------
    | Domēna vārds
    |--------------------------------------------------------------------------
    |
    | Aktīvās direktorijas domēns
    */   
    
    'account_suffix' => '@gatech.edu',
    
    /*
    |--------------------------------------------------------------------------
    | Domēna kontrolieris
    |--------------------------------------------------------------------------
    |
    | Aktīvās direktorijas domēna kontrolieris
    */   
    
    'domain_controller' => 'whitepages.gatech.edu',
    
    /*
    |--------------------------------------------------------------------------
    | DN
    |--------------------------------------------------------------------------
    |
    */   
    
    'base_dn' => 'dc=whitepages,dc=gatech,dc=edu',
    
    /*
    |--------------------------------------------------------------------------
    | Administratora lietotāja vārds
    |--------------------------------------------------------------------------
    |
    | Jānorāda lietotāja vārds ar Aktīvās Direktorijas administratora tiesībām
    */   
    
    'admin_username' => '',
    
    /*
    |--------------------------------------------------------------------------
    | Administratora lietotāja parole
    |--------------------------------------------------------------------------
    |
    | Jānorāda administratora lietotāja parole
    */   
    
    'admin_password' => '',
];