<?php

/*
|--------------------------------------------------------------------------
|Labels for CMS register (grid and form labels) - table edu_programms_avail
|--------------------------------------------------------------------------
*/
return [
    'list_name' => 'Programmu pieejamība',
    'item_name' => 'Programmu pieejamība',
    
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
            'title' => 'Izpildot zināšanu pārbaudījumu',
            'code' => 'AFTER_TEST'
        ],
        [
            'title' => 'Sekmīgs zināšanu pārbaudījums',
            'code' => 'AFTER_TEST_OK'
        ],
    ],
];