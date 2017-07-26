<?php

/*
|--------------------------------------------------------------------------
|Labels for CMS register (grid and form labels) - table edu_modules_avail
|--------------------------------------------------------------------------
*/
return [
    'list_name' => 'Moduļu pieejamība',
    'item_name' => 'Moduļu pieejamība',
    
    'title' => 'Nosaukums',
    'code' => 'Kods',
        
    'values' => [
        [
            'title' => 'Publiska - visiem lietotājiem',
            'code' => 'PUBLIC'
        ],
        [
            'title' => 'Autentificētiem lietotājiem',
            'code' => 'AUTH_ALL'
        ],
        [
            'title' => 'Valsts pārvaldes darbiniekiem',
            'code' => 'AUTH_ORG'
        ],
        [
            'title' => 'Tikai ar uzaicinājumu',
            'code' => 'INVITE'
        ],
    ],
];