<?php

/*
|--------------------------------------------------------------------------
| Labels for employee profile
|--------------------------------------------------------------------------
*/
return [
    
    'err_no_edit_rights' => 'Jums nav tiesību rediģēt izvēlētā profila datus!',
    'err_first_save_new_item' => 'Lūdzu, vispirms saglabājiet jaunā darbinieka pamatdatus un tad varēsiet pievienot saistītos datus.',
    'hint_view_profile' => 'Skatīt profilu',
    
    // fields tabs (must be equal with tab titles defined in CMS form - in database table dx_tabs)    
    'tab_general' => 'General',
    'tab_pdetails' => 'Personal details',
    'tab_wdetails' => 'Work details',
    'tab_wplace' => 'Workplace',
    'tab_cdetails' => 'Contact details',
    'tab_addr' => 'Addresses',
    
    'qualif_menu' => 'Qualification',
    
    // Subgrid tabs for qualification (must be equal with tab titles defined in CMS form - in database table dx_tabs)
    'tab_lang' => 'Languages',
    'tab_links' => 'Links',
    'tab_educ' => 'Education',
    'tab_cert' => 'Certificates',
    'tab_cv' => 'CVs & other', 
    
    'assets_menu' => 'Assets',
    
    // Subgrid tabs for assets (must be equal with tab titles defined in CMS form - in database table dx_tabs)
    'tab_cards' => 'Corporate cards',
    'tab_devices' => 'Devices',
    
    'personal_docs' => [
        'new_doc' => 'Jauns dokuments',
        'clear_doc' => 'Notīrīt dokumenta datus',
        'save_docs' => 'Saglabāt dokumentus',
        'country' => 'Valsts',
        'personal_doc_type' => 'Dokumenta tips',
        'doc_nr' => 'Dokumenta numurs',
        'valid_to' => 'Derīgs līdz',
        'publisher' => 'Izdevējs',
        'file' => 'Datne',
    ],    
    
    'notes' => [
        'type_hint' => 'Rakstiet piezīmi šeit...',
        'delete_note_title' => 'Piezīmes dzēšana',
        'delete_note_text' => 'Vai Jūs tiešām vēlaties dzēst piezīmi?',
        'note_missing' => "Piezīme neeksistē!",
        'modified' => "Laboja",
        'who_can_see' => 'Kurš var redzēt šo piezīmi'
    ], 
    
    'timeoff' => [
        'accrued' => 'Uzkrāts',
        'used' => 'Izlietots',
        'balance' => 'Atlikums',
        'menu_actions' => 'Iespējas',
        'accrual_policy' => 'Uzkrāšanas politika',
        'calculate' => 'Aprēķināt',
        'delete_accrual' => 'Dzēst aprēķināto',

        'delete_confirm' => 'Vai tiešām dzēst aprēķināto dienu uzkrājumu norādītajam prombūtnes veidam?',
        
        'history' => 'Vēsture',
        'timeoff' => 'Prombūtne',
        'date_interval' => 'Datuma periods',
        'total_accrued' => 'Kopā uzkrāts',
        'total_used' => 'Kopā izlietots',
        'from_date' => 'No datuma',
        'to_date' => 'Līdz datumam',
        'date' => 'Datums',
        'type' => 'Tips',
        'notes' => 'Piezīmes',
        'used_accrued' => 'Izlietots / Uzkrāts (stundas)',
        'balance_hours' => 'Atlikums (stundas)',
        'chart' => 'Diagramma',
        'table' => 'Tabula',
        'available' => 'pieejamas'
    ], 
];
