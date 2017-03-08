<?php

/*
|--------------------------------------------------------------------------
| Labels for CMS register (grid and form labels) - table dx_views
|--------------------------------------------------------------------------
*/
return [
    
    // Form tab
    'tab_title' => 'Kontrole',
    'js_showhide' => "Parāda vai paslēpj laukus atkarībā no izvēlētās opcijas 'Ir ikdienas kontrole' vērtības",
    'js_showhide_emails' => "Parāda vai paslēpj laukus atkarībā no izvēlētās opcijas 'Ir notifikāciju sūtīšana' vērtības",
    
    // Field is_for_monitoring
    'is_for_monitoring_list' => 'Ir ikdienas kontrole',
    'is_for_monitoring_form' => 'Ir ikdienas kontrole',
    'is_for_monitoring_hint' => 'Norāda, vai sistēmas process katru dienu kontrolēs un auditēs šī skata ierakstu skaitu.',
    
    // Field is_email_sending
    'is_email_sending_list' => 'Ir notifikāciju sūtīšana',
    'is_email_sending_form' => 'Ir notifikāciju sūtīšana',
    'is_email_sending_hint' => 'Norāda, vai sistēmas process izsūtīs notifikācijas e-pastus, ja skatā ir kāds ieraksts.',
    
    // Field email_receivers
    'email_receivers_list' => 'Notifikāciju adresātu e-pasti',
    'email_receivers_form' => 'Notifikāciju adresātu e-pasti',
    'email_receivers_hint' => 'Norāda notifikāciju adresātu e-pastus atdalītus ar semikolonu.',
    
    // Field role_id
    'role_id_list' => 'Loma',
    'role_id_form' => 'Loma',
    'role_id_hint' => 'Loma, kuras lietotāji saņems notifikācijas e-pastus, ja šajā skatā būs kāds ieraksts.',
    
    // Field field_id
    'field_id_list' => 'Darbinieka lauks',
    'field_id_form' => 'Darbinieka lauks',
    'field_id_hint' => 'Norāda lauku no šī skata, kas satur informāciju par atbildīgo darbinieku, kuram tiks nosūtīts notifikācijas e-pasts, ja šajā skatā būs kāds ieraksts, kuram kā atbildīgais ir norādīts attiecīgais darbinieks.',
    
     // Field is_detailed_notify
    'is_detailed_notify_list' => 'Ir detalizēta notifikācija',
    'is_detailed_notify_form' => 'Ir detalizēta notifikācija',
    'is_detailed_notify_hint' => 'Norāda, vai notifikāciju adresāti (no lomas vai norādītie e-pasti) saņems detalizētu informāciju (dokumenta numurs, apraksts) par visiem ierakstiem, kas atbilst skata nosacījumiem. Ja šo opciju nenorāda, tad notifikācijā tiks ietverta tikai vispārīga informācija - kontroles nosacījuma nosaukums un atbilstošo ierakstu skaits.',

];