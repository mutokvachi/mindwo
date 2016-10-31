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
     * OpenLDAP host address
     * @var string 
     */
    protected $ldap_host;

    /**
     * OppenLDAP connection port
     * @var string 
     */
    protected $ldap_port;

    /**
     * OpenLDAP root DN
     * @var string 
     */
    protected $ldap_root_dn;

    /**
     * OpenLDAP password for accessing root DN
     * @var string 
     */
    protected $ldap_root_password;

    /**
     * OpenLDAP's DN which is used to search for users. This is used when authenticating user. 
     * @var string 
     */
    protected $ldap_search_dn;
    
    /**
     * OpenLDAP's filter which is used when system tries to find user's data (DN + information) for authentication
     * @var string 
     */
    protected $ldap_search_filter;

    /**
     * Initiate class and retrieves requiered connection information from configuration files
     */
    public function __construct()
    {
        $this->ldap_host = Config::get('open_ldap.host');
        $this->ldap_port = Config::get('open_ldap.port');
        $this->ldap_root_dn = Config::get('open_ldap.root_dn');
        $this->ldap_root_password = Config::get('open_ldap.root_password');
        $this->ldap_search_dn = Config::get('open_ldap.search_dn');
        $this->ldap_search_path = Config::get('open_ldap.search_path');
    }

    /**
     * Authenticate user using OpenLDAP
     * @param \App\User $user_row Data row containign users data
     * @param string $user_name users login name
     * @param string $user_password User password
     * @return boolean Result if authentication succeeded
     */
    public function auth($user_row, $user_name, $user_password)
    {
        if (empty($user_name) || empty($user_password)) {
            return false;
        }

        $conn = ldap_connect($this->ldap_host, $this->ldap_port);

        // PHP Fatal error here means that you need to install php-ldap extension
        // Invalid credentials
        if (!$conn) {
            Log::error('Incorrect connection data for OpenLDAP');
            return false;
        }

        ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);

        // First try to bind to OpenLDAP using root DN. Root bind will be used to find user's DN
        if (ldap_bind($conn, $this->ldap_root_dn, $this->ldap_root_password)) {
            return $this->authenticateLDAP($conn, $user_row, $user_name, $user_password);
        } else {
            Log::error('Bind failed for OpenLDAP. Incorrect root DN username or password.');
            return false;
        }
    }

    /**
     * If user got authenticated then authorize user. 
     * Creates new user if user doesn't exist and if it is allowed to create user from LDAP user
     * @param \App\User $user_row Users data row to check if user exists
     * @param array $user_ldap_data Users datat which are retrieved from LDAP
     * @return boolean Returns status if user has been authenticated
     */
    private function prepareAuthorization($user_row, $user_ldap_data)
    {
        if ($user_row) {
            AuthHelper::authorizeUser($user_row);

            return true;
        }

        if (!Config::get('auth.create_user_if_not_exist')) {
            return false;
        }

        $user_email = $user_ldap_data[0]['mail'][0];
        $user_first_name = $user_ldap_data[0]['givenname'][0];
        $user_last_name = $user_ldap_data[0]['sn'][0];
        $user_position_title = $user_ldap_data[0]['title'][0];

        $user = AuthHelper::getUser($user_email, $user_first_name, $user_last_name, $user_position_title);

        AuthHelper::authorizeUser($user);

        return true;
    }

    /**
     * Try to authenticate user using OpenLDAP
     * @param object $conn OpenLDAP connection
     * @param \App\User $user_row Users data row to check if user exists
     * @param string $user_name users login name
     * @param string $user_password User password
     * @return boolean Returns true if user authenticated and successfully authorized
     */
    function authenticateLDAP($conn, $user_row, $user_name, $user_password)
    {
        // Retrieving user data and appropriate user's DN which will be used to bind user
        $query = ldap_search(
                $conn, $this->ldap_search_dn, // DN for user accounts
                "(mail=$user_name)" . $this->ldap_search_path, // filter query
                ['uid', 'mail', 'userpassword', 'givenname', 'sn', 'title'] // attributes to return
        );

        $result = ldap_get_entries($conn, $query);

        if (!$result['count']) {
            return false;
        }

        // Retrieved users DN which is used for binding
        $user_dn = $result[0]['dn'];

        // If bind doesn't succceed then authentication failed
        if (ldap_bind($conn, $user_dn, $user_password)) {
            // After authentication it is needed to check if such user exists in system and authorize user 
            return $this->prepareAuthorization($user_row, $result);
        } else {
            return false;
        }
    }
}
