<?php

/*
|--------------------------------------------------------------------------
|Labels for CMS register (grid and form labels) - table dx_pages
|--------------------------------------------------------------------------
*/
return [
    'list_name' => 'Lapas',
    'item_name' => 'Lapa',
    
    // tabs
    'tab_main' => 'Pamatdati',
    'tab_content' => 'Lapas saturs',
    'tab_roles' => 'Lomas',

    // fields
    'title' => 'Nosaukums',
    'is_active' => 'Ir aktīva',
    'group_id' => 'Portāla veids',
    'group_id_hint' => 'Tā kā satura vadības sistēma var tikt lietota vairāku portālu pārvaldībai, tad šajā laukā jānorāda portāls, kurā lapa būs pieejama.',

    'custom_pages' => [
        [
            'title' => 'E-pasts',
            'url_title' => 'mail',
            'group_id' => 1,
        ],
        [
            'title' => 'Sistēmas konstruktors',
            'url_title' => 'constructor',
            'group_id' => 1,
        ],
        [
            'title' => 'Organizācijas struktūra',
            'url_title' => 'organization',
            'group_id' => 1,
        ],
        [
            'title' => 'Mācību grupu plānošana',
            'url_title' => 'calendar/scheduler',
            'group_id' => 1,
        ],
        [
            'title' => 'Mācību grupu komplektēšana',
            'url_title' => 'calendar/complect',
            'group_id' => 1,
        ],
        [
            'title' => 'Jauna reģistra izveidošana',
            'url_title' => 'constructor/register/create',
            'group_id' => 1,
        ],
        [
            'title' => 'Navigācijas iestatīšana',
            'url_title' => 'constructor/menu/1',
            'group_id' => 1,
        ],
    ],
];