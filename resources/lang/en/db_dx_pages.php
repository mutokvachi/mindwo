<?php

/*
|--------------------------------------------------------------------------
|Labels for CMS register (grid and form labels) - table dx_pages
|--------------------------------------------------------------------------
*/
return [
    'list_name' => 'Pages',
    'item_name' => 'Page',
    
    // tabs
    'tab_main' => 'General',
    'tab_content' => 'Page content',

    // Fields
    'tab_roles' => 'Roles',
    'title' => 'Title',
    'is_active' => 'Is active',
    'group_id' => 'Portal',
    'group_id_hint' => 'In case CMS is used for multiple portals management here we can set for which portal page is available.',

    'custom_pages' => [
        [
            'title' => 'E-mail',
            'url_title' => 'mail',
            'group_id' => 1,
        ],
        [
            'title' => 'System constructor',
            'url_title' => 'constructor',
            'group_id' => 1,
        ],
        [
            'title' => 'Organization structure',
            'url_title' => 'organization',
            'group_id' => 1,
        ],
        [
            'title' => 'Education groups planning',
            'url_title' => 'calendar/scheduler',
            'group_id' => 1,
        ],
        [
            'title' => 'Education groups complecting',
            'url_title' => 'calendar/complect',
            'group_id' => 1,
        ],
        [
            'title' => 'New register creation',
            'url_title' => 'constructor/register/create',
            'group_id' => 1,
        ],
        [
            'title' => 'Navigation configuration',
            'url_title' => 'constructor/menu/1',
            'group_id' => 1,
        ],
    ],
];