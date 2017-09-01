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
    'err_action_edit_subject' => 'Skatīt pasākumu',
    'err_action_edit_module' => 'Skatīt moduli',
    'err_action_edit_programm' => 'Skatīt programmu',
    'err_action_edit_calendar' => 'Skatīt kalendārā',
    'err_action_edit_pause' => 'Skatīt kafijas pauzi',
    
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
            'title' => 'Nodarbības kopējais laiks nav vienāds ar pasniedzēju kopējo laiku',
            'code' => 'TEACHER_TIME_NOT_COVER',
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
            'title' => 'Grupas nodarbībai norādītā telpa ir paredzēta arī citas grupas kafijas pauzei',
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
            'title' => 'Grupas dalībnieku skaits nav vismaz 50%',
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
        [
            'title' => 'Uz komplektēšanu var nodot tikai iekšējās grupas - grupa nav iekšējā',
            'code' => 'NOT_INNER',
            'is_for_publish' => false,
            'is_for_complect' => true,
        ],
        [
            'title' => 'Grupa jau ir komplektēšanā',
            'code' => 'NOT_COMPLECT',
            'is_for_publish' => false,
            'is_for_complect' => true,
        ],
        [
            'title' => 'Grupa jau ir bijusi komplektēšanā',
            'code' => 'NOT_COMPLECT_AGAIN',
            'is_for_publish' => false,
            'is_for_complect' => true,
        ],
        [
            'title' => 'Grupai nav norādīts galvenais pasniedzējs, kura paraksts tiks ievietots sertifikātā',
            'code' => 'NO_MAIN_TEACHER',
            'is_for_publish' => true,
            'is_for_complect' => false,
        ],
    ],
];