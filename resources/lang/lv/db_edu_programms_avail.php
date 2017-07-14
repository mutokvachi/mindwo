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
            'title' => 'Publiski pieejama visiem lietotājiem',
            'code' => 'PUBLIC'
        ],
        [
            'title' => 'Pieejama autentificētiem lietotājiem',
            'code' => 'AUTH_ALL'
        ],
        [
            'title' => 'Pieejama valsts pārvaldes darbiniekiem',
            'code' => 'AUTH_ORG'
        ],
        [
            'title' => 'Pieejama pēc zināšanu pārbaudījuma izpildes',
            'code' => 'AFTER_TEST'
        ],
        [
            'title' => 'Pieejama pēc sekmīgas zināšanu pārbaudījuma izpildes',
            'code' => 'AFTER_TEST_OK'
        ],
    ],
];