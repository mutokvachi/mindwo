<?php

namespace App\Libraries\Auth;

use Config;
use Log;
use \App\Libraries\Auth\AuthHelper;

/**
 * Authenticate user using OpenLDAP
 */
class OpenLDAP
{
    /**
     * Open LDAP host address
     * @var string 
     */
    protected $ldap_host;
    
    /**
     * Oppen LDAP connection port
     * @var string 
     */
    protected $ldap_port;
    
    /**
     * Open LDAP root DN
     * @var string 
     */
    protected $ldap_root_dn;
    
    /**
     * Open LDAP password for accessing root DN
     * @var string 
     */
    protected $ldap_root_password;
    
    /**
     * Open LDAP Accoutn prefix which is added in the front of given user name. This is used when authenticating user.
     * @var string 
     */
    protected $ldap_account_prefix;
    
    /**
     * Open LDAP Accoutn suffix which is added in the end of given user name. This is used when authenticating user. 
     * @var string 
     */
    protected $ldap_account_suffix;

    /**
     * Initiate class and retrieves requiered connection information from configuration files
     */
    public function __construct()
    {
        $this->ldap_host = Config::get('open_ldap.host');
        $this->ldap_port = Config::get('open_ldap.port');
        $this->ldap_root_dn = Config::get('open_ldap.root_dn');
        $this->ldap_root_password = Config::get('open_ldap.root_password');
        $this->ldap_account_prefix = Config::get('open_ldap.account_prefix');
        $this->ldap_account_suffix = Config::get('open_ldap.account_suffix');
    }

    /**
     * Authenticate user using OpenLDAP
     * @param type $user_row Data row containign users data
     * @param type $user_name users login name
     * @param type $user_password User password
     * @return boolean Result if authentication succeeded
     */
    public function auth($user_row, $user_name, $user_password)
    {
        // Establishing connection to LDAP server
        $conn = ldap_connect($this->ldap_host, $this->ldap_port);

        // PHP Fatal error here means that you need to install php-ldap extension
        // Invalid credentials
        if (!$conn) {
            return false;
        }

        // Setting protocol version
        ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);

        $user_dn = $this->ldap_account_prefix . $user_name . $this->ldap_account_suffix;

        // Bind to LDAP server
        if (ldap_bind($conn, $user_dn, $user_password)) {
            return $this->prepareAuthorization($conn, $user_row, $user_dn, $user_name);
        } else {
            return false;
        }
    }
    
    /**
     * Check if user got authenticated and then authorize user. 
     * Creates new user if user doesn't exist and if it is allowed to create user from LDAP user
     * @param object $conn LDAP connection
     * @param \App\User $user_row Users data row to check if user exists
     * @param string $user_dn Users DN path
     * @param string $user_name Users login name which is his password
     * @return boolean Returns status if user has been authenticated
     */
    private function prepareAuthorization($conn, $user_row, $user_dn, $user_name)
    {
        if ($user_row) {
            AuthHelper::authorizeUser($user_row);

            return true;
        }

        if (!Config::get('auth.create_user_if_not_exist')) {
            return false;
        }

        $query = ldap_search(
                $conn, $user_dn, // DN for user accounts
                "(mail=$user_name)", // filter query
                ['mail', 'givenname', 'sn', 'title'] // attributes to return
        );

        $result = ldap_get_entries($conn, $query);

        if (!$result['count']) {
            return false;
        }

        $user_email = $result[0]['mail'][0];
        $user_first_name = $result[0]['givenname'][0];
        $user_last_name = $result[0]['sn'][0];
        $user_position_title = $result[0]['title'][0];
        
        $user = AuthHelper::getUser($user_email, $user_first_name, $user_last_name, $user_position_title);

        AuthHelper::authorizeUser($user);

        return true;
    }
}