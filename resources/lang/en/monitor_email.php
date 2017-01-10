<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Monitoring email notification labels and texts
    |--------------------------------------------------------------------------
    */

    'heading' => 'System notification',
    'intro' => 'This is notification regarding %s records/documents for which you are responsible.',
  
    'lbl_view' => 'Monitoring rule',
    'lbl_count' => 'Record count',
    'lbl_meta' => 'Records information',
    'lbl_reg_nr' => 'ID/Reg. nr.',
    'lbl_about' => 'Details',
        
    // %s - email
    'info_sent' => 'Information regarding records/documents was sent to this e-mail: %s. If you received this email by mistake, please inform IT support.',
    
    // 1st %s - system name, 2nd %s - date/time when email was sent
    'info_sys' => 'This e-mail was sent from the system %s on %s.',

    // for general monitoring notification
    'intro_general_n' => 'It is detected %s conditions which are monitored by %s.',
    'intro_general_1' => 'It is detected %s condition which is monitored by %s.',
    'lbl_control' => 'Information about monitored conditions',
    'lbl_control_title' => 'Monitoring rule',
    'lbl_control_count' => 'Count',
    'info_count' => 'Record counts might be different, because data can be allready changed.',
    
];