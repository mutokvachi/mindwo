<?php

namespace App\Libraries\Auth;

use Config;

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
        $config = array(
            'account_suffix' => Config::get('ldap.account_suffix'),
            'domain_controllers' => array(Config::get('ldap.domain_controller')),
            'base_dn' => Config::get('ldap.base_dn'),
            'admin_username' => Config::get('ldap.admin_username'),
            'admin_password' => Config::get('ldap.admin_password'),
        );

        $ad = new Adldap($config);

        if ($ad->authenticate($user_name, $user_password)) {
            Auth::loginUsingId($user_row->id);
            return true;
        }

        return false;
    }
}