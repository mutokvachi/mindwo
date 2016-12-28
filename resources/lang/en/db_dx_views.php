<?php

/*
|--------------------------------------------------------------------------
| Labels for CMS register (grid and form labels) - table dx_views
|--------------------------------------------------------------------------
*/
return [
    
    // Form tab
    'tab_title' => 'Control',
    'js_showhide' => "Show or hide fields regarding of the option 'Is daily monitoring' value",
    'js_showhide_emails' => "Show or hide fields regarding of the option 'Is notification emails enabled' value",
    
    // Field is_for_monitoring
    'is_for_monitoring_list' => 'Is daily monitoring',
    'is_for_monitoring_form' => 'Is daily monitoring',
    'is_for_monitoring_hint' => 'Indicates if the system process will monitor and audit this view record count.',
    
    // Field is_email_sending
    'is_email_sending_list' => 'Is notification emails enabled',
    'is_email_sending_form' => 'Is notification emails enabled',
    'is_email_sending_hint' => 'Indicates if an email will be send to receivers in case daily process finds some rows in this view.',
    
    // Field email_receivers
    'email_receivers_list' => 'Notification email receivers',
    'email_receivers_form' => 'Notification email receivers',
    'email_receivers_hint' => 'Enter notifications receiver emails. It is possible to enter several emails seperated by semicolon.',
        
    // Field role_id
    'role_id_list' => 'Loma',
    'role_id_form' => 'Loma',
    'role_id_hint' => 'Loma, kuras lietotāji saņems notifikācijas e-pastus, ja šajā skatā būs kāds ieraksts.',
    
    // Field field_id
    'field_id_list' => 'Darbinieka lauks',
    'field_id_form' => 'Darbinieka lauks',
    'field_id_hint' => 'Norāda lauku no šī skata, kas satur informāciju par atbildīgo darbinieku, kuram tiks nosūtīts notifikācijas e-pasts, ja šajā skatā būs kāds ieraksts, kuram kā atbildīgais ir norādīts attiecīgais darbinieks.',
    
];