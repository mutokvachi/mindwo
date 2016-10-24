<?php
namespace App\Libraries\Auth;

use Auth;

/**
 * Contains helper functions fort authenticating users
 */
class AuthHelper
{
    /**
     * Search user which is linked with LDAP user, if doesn't exist then create a new one
     * @param string $user_email E-mail address for user
     * @param string $user_first_name First name for user
     * @param string $user_last_name Last name for user
     * @param string $user_position_title Position's title for user
     * @return \App\User Retrieved user
     */
    public static function getUser($user_email, $user_first_name, $user_last_name, $user_position_title)
    {
        $user = \App\User::where('email', '=', $user_email)->first();

        if (!$user) {
            $user = new \App\User();

            $user->email = $user_email;
            $user->ad_login = $user_email;
            $user->first_name = $user_first_name;
            $user->last_name = $user_last_name;
            $user->position_title = $user_position_title;

            $user->save();
        }

        return $user;
    }

    /**
     * Authroize given user into system
     * @param \App\User $user Object for authenticated user
     */
    public static function authorizeUser($user)
    {
        if (Auth::user()) {
            Auth::logout();
        }

        Auth::login($user);
    }
}