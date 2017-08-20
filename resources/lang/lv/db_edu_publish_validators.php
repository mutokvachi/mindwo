<?php

/*
|--------------------------------------------------------------------------
|Labels for CMS register (grid and form labels) - table edu_publish_validators
|--------------------------------------------------------------------------
*/
return [
    'list_name' => 'Datu pārbaudes',
    'item_name' => 'Datu pārbaude',
    
    'title' => 'Nosaukums',
    'code' => 'Kods',
    'is_for_publish' => 'Ir publicēšanas pārbaude',
    'is_for_complect' => 'Ir komplektēšanas pārbaude',
   
    'err_action_edit_group' => 'Skatīt grupu',
    
    'values' => [
        [
            'title' => 'Grupai nav norādīta neviena nodarbība',
            'code' => 'NO_DAY',
            'is_for_publish' => true,
            'is_for_complect' => true,
        ],
        [
            'title' => 'Nodarbībai nav norādīts neviens pasniedzējs',
            'code' => 'NO_TEACHER',
            'is_for_publish' => true,
            'is_for_complect' => true,
        ],
        [
            'title' => 'Nodarbības pasniedzējiem pārklājas laiki',
            'code' => 'TEACHER_TIME_OVERLAP',
            'is_for_publish' => true,
            'is_for_complect' => true,
        ],
        [
            'title' => 'Mācību pasākums nav publicēts',
            'code' => 'NOT_PUBLISHED_SUBJECT',
            'is_for_publish' => true,
            'is_for_complect' => true,
        ],
        [
            'title' => 'Mācību modulis nav publicēts',
            'code' => 'NOT_PUBLISHED_MODULE',
            'is_for_publish' => true,
            'is_for_complect' => true,
        ],
        [
            'title' => 'Mācību programma nav publicēta',
            'code' => 'NOT_PUBLISHED_PROGRAMM',
            'is_for_publish' => true,
            'is_for_complect' => true,
        ],
        [
            'title' => 'Grupas vietu limits pārsniedz vietu limitu telpā, kurā notiks nodarbība',
            'code' => 'GROUP_LIMIT_MORE',
            'is_for_publish' => true,
            'is_for_complect' => true,
        ],
        [
            'title' => 'Pārklājas grupu nodarbību laiki kādā no telpām',
            'code' => 'GROUP_TIME_OVERLAP',
            'is_for_publish' => true,
            'is_for_complect' => true,
        ],
        [
            'title' => 'Grupas nodarbībai norādītā telpa ir paredzēta arī kafijas pauzei',
            'code' => 'COFFEE_TIME_OVERLAP',
            'is_for_publish' => true,
            'is_for_complect' => true,
        ],
        [
            'title' => 'Kafijas pauzei nav norādīts pakalpojuma sniedzējs',
            'code' => 'NO_COFFEE_PROVIDER',
            'is_for_publish' => true,
            'is_for_complect' => true,
        ],
        [
            'title' => 'Dalībnieku kvotu summa pa uzaicināmajām iestādēm nav vienāda ar grupas vietu limitu',
            'code' => 'QUOTAS_SUM_NOT_EQUAL',
            'is_for_publish' => false,
            'is_for_complect' => true,
        ],
        [
            'title' => 'Grupas dalībnieku skaits nav pietiekošs',
            'code' => 'MEMBERS_NOT_ENOUGH',
            'is_for_publish' => true,
            'is_for_complect' => false,
        ],
        [
            'title' => 'Grupas dalībnieku skaits pārsniedz grupas vietu limitu',
            'code' => 'MEMBERS_TOO_MUCH',
            'is_for_publish' => true,
            'is_for_complect' => false,
        ],
    ],
];