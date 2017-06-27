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
    'tab_general' => 'Pamatinformācija',
    'tab_pdetails' => 'Par personu',
    'tab_wdetails' => 'Darba attiecības',
    'tab_wplace' => 'Darbavieta',
    'tab_cdetails' => 'Kontaktinformācija',
    'tab_addr' => 'Adreses',
    
    'tab_salary' => 'Algas',
    'tab_shares' => 'Akcijas',
    'tab_polygraph' => 'Pārbaudes',
    
    'qualif_menu' => 'Kvalifikācija',
    'confidential_menu' => 'Konfidenciāli',
    
    // Subgrid tabs for qualification (must be equal with tab titles defined in CMS form - in database table dx_tabs)
    'tab_lang' => 'Valodas',
    'tab_links' => 'Saites',
    'tab_educ' => 'Izglītība',
    'tab_cert' => 'Sertifikāti',
    'tab_cv' => 'CV u.c.', 
    
    'tab_documents' => 'Dokumenti',
    'tab_timeoff' => 'Prombūtnes',
    'tab_notes' => 'Piezīmes',
    'tab_info' => 'Info',
    
    'assets_menu' => 'Pamatlīdzekļi',
    
    // Subgrid tabs for assets (must be equal with tab titles defined in CMS form - in database table dx_tabs)
    'tab_cards' => 'Bankas kartes',
    'tab_devices' => 'Iekārtas',
    
    'direct_reporters' => 'Tiešajā pakļautībā',
    
    'lbl_about' => 'Vispārīgi',
    'lbl_edit' => 'Rediģēt',
    'lbl_save' => 'Saglabāt',
    'lbl_cancel' => 'Atcelt',
    
    'avail_left' => 'Nestrādā',
    'avail_active' => 'Aktīvs',
    'avail_potential' => 'Potenciāls',
    
    'hint_left' => 'Darbinieks no %s vairs nestrādā uzņēmumā',
    'hint_active' => 'Darbinieks strādā uzņēmumā',
    'hint_potential' => 'Darbinieks tiek pieņemts darbā',
    
    'lbl_direct_superv' => 'Tiešais vadītājs',
    
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
