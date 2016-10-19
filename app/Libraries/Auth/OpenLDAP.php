<?php

namespace App\Libraries\Auth;

use Config;
use Log;

/**
 * Authenticate user using OpenLDAP
 */
class OpenLDAP
{
    protected $ldap_host;
    protected $ldap_port;
    protected $ldap_root_dn;
    protected $ldap_root_password;

    /**
     * Initiate class and retrieves requiered connection information from configuration files
     */
    public function __construct()
    {
        $this->ldap_host = Config::get('open_ldap.host');
        $this->ldap_port = Config::get('open_ldap.port');
        $this->ldap_root_dn = Config::get('open_ldap.root_dn');
        $this->ldap_root_password = Config::get('open_ldap.root_password');
    }

    /**
     * Authenticate user using OpenLDAP
     * @param type $user_name users login name
     * @param type $user_password User password
     * @return boolean Result if authentication succeeded
     */
    public function auth($user_name, $user_password)
    {
        /* $config = [
          'host' => 'ldaps://virt.blizko.lv',
          'bindDN' => 'cn=Manager,dc=9009,dc=in',
          'bindPW' => 'jRztNGVlBudwvnG',
          'usersDN' => 'mail=ztest1@bitfury.org,ou=Users,domainName=bitfury.org,o=domains,dc=9009,dc=in'
          ];

          $test_email = 'ztest1@bitfury.org';
          $test_password = 'jRztNGVlBudwvnG'; */
        // Establishing connection to LDAP server
        $conn = ldap_connect($this->ldap_host, $this->ldap_port);

        // PHP Fatal error here means that you need to install php-ldap extension
        // Invalid credentials
        if (!$conn) {
            return false;
        }

        // Setting protocol version
        ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);

        // Bind to LDAP server
        if (ldap_bind($conn, $this->ldap_root_dn, $this->ldap_root_password)) {
            return true;
        } else {
            return false;
        }

        /*
          list_users();
          echo "\n";
          check_password($test_email, $test_password); */
    }

    // List all users with email address specified
    function list_users()
    {
        global $conn, $config;

        echo "Listing all users having email:\n";
        $query = ldap_search($conn, $config['usersDN'], "(mail=*)");
        $result = ldap_get_entries($conn, $query);

        for ($i = 0; $i < $result['count']; $i++) {
            echo "dn: {$result[$i]['dn']}, mail: {$result[$i]['mail'][0]}";
            echo "\n";
        }
    }

    function check_password($mail, $password)
    {
        global $conn, $config;

        echo "Verifying user's password ($mail, $password):\n";

        // Retrieving user data
        $query = ldap_search(
                $conn, $config['usersDN'], // DN for user accounts
                "(mail=$mail)", // filter query
                ['mail', 'userpassword'] // attributes to return
        );
        $result = ldap_get_entries($conn, $query);

        if (!$result['count']) {
            echo "No such user\n";
            return false;
        }

        echo "Found user with email $mail\n";

        $hash = $result[0]['userpassword'][0];

        echo "Password hash from LDAP: $hash\n";

        // Extracting salt from hash
        $salt = substr(base64_decode(substr($hash, 6)), 20);

        // Encrypting password
        $encPassword = '{SSHA}' . base64_encode(sha1($password . $salt, true) . $salt);

        echo "Our encrypted password: $encPassword\n";

        // Comparing our encrypted password to hash from LDAP
        if ($hash != $encPassword) {
            echo "No such email/password combination\n";
            return false;
        }

        echo "Email and password are OK\n";
        return true;

        // Now you can authorize user as usual
    }
}