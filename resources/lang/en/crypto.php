<?php

/**
 * Crypto language file for English
 */
return [
    'user_profile_title' => "Encryption settings",
    'decrypt_btn' => 'Decrypt data',
    'btn_accept' => 'Accept',
    'btn_close' => 'Close',
    'label_password' => 'Encryption password',
    'label_password_again' => 'Encryption password again',
    'title_modal_password' => 'Your encryption password',
    'title_modal_generate_cert' => 'Generate certificate',
    'btn_generate_cert' => 'Generate certificate',
    'btn_generate_new_cert' => 'Generate new certificate',
    
    'e_unknown' => 'Uknown error',
    'e_user_not_exists' => 'Specified user does not exists',
    'e_current_user_missing_cert' => "You have not generated Your encryption certificate. To continue please generate Your encryption certificate!",
    'e_specified_user_missing_cert' => "User \":name\" hasn't generated encryption certificate. To continue user must generate encryption certificate!",
    'e_save' => 'Error while saving data',
    'e_cert_both_psw' => 'Fill both password fields!',
    'e_cert_psw_short' => 'Password must be at least 8 symbols long!',
    'e_cert_psw_not_match' => 'Passwords do not match!',
    'e_get_user_cert' => "Error while retrieving your certificate",
    'e_password_incorrect' => 'Password is not correct',
    'i_save_masterkey_success' => 'Master key successfully generated and saved',
    'i_save_cert_success' => 'Certificate successfully generated and saved',
    'w_confirm_generate_new_cert' => '<b>Do You realy want to generate a new certificate?</b></br>Your access for encrypted data will be lost - access can be restored only by someone else who has access for encrypted data.',
    
    'db'=>[
        'user_id' => 'User',
        'master_key' => 'Master key',
        'public_key' => 'Public key',
        'private_key' => 'Private key',
        'master_key_group_title' => 'Master key group',
    ]
];
