<?php

namespace App\Libraries\Auth;

use Config;
use Adldap\Adldap;

/**
 * Authenticate user using Active Directory
 * Used AD helper library: https://github.com/adldap/adLDAP
 */
class ActiveDirectory
{

    /**
     * Authenticate user using Active Directory
     * @param type $user_row Data row containign users data
     * @param type $user_name users login name
     * @param type $user_password User password
     * @return boolean Result if authentication succeeded
     */
    public function auth($user_row, $user_name, $user_password)
    {        
        if(empty($user_name) || empty($user_password)){
            return false;
        } 
        
        return false;
        
        $config = array(
            'account_suffix' => Config::get('ldap.account_suffix'),
            'domain_controllers' => array(Config::get('ldap.domain_controller')),
            'base_dn' => Config::get('ldap.base_dn'),
            'admin_username' => Config::get('ldap.admin_username'),
            'admin_password' => Config::get('ldap.admin_password'),
        );

        $provider = new \Adldap\Connections\Provider($config);

        if ($provider->auth()->bind($user_name, $user_password)) {
            // Credentials were correct.
            return $this->prepareAuthorization($user_row, $user_name);
        } else {
            // Credentials were incorrect.
            return false;
        }
    }

    /**
     * Check if user got authenticated and then authorize user. 
     * Creates new user if user doesn't exist and if it is allowed to create user from LDAP user
     * @param \App\User $user_row Users data row to check if user exists
     * @param string $user_name Users login name which is his password
     * @return boolean Returns status if user has been authenticated
     */
    private function prepareAuthorization($user_row, $user_name)
    {
        if ($user_row) {
            AuthHelper::authorizeUser($user_row);

            return true;
        }

        if (!Config::get('auth.create_user_if_not_exist')) {
            return false;
        }

        $user = AuthHelper::getUser($user_name, '', '', '');

        AuthHelper::authorizeUser($user);

        return true;
    }
}