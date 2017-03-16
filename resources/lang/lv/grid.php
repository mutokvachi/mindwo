<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Sarakstu nosaukumi
    |--------------------------------------------------------------------------
    |
    | Nosaukumi, kas izmantoti sarakstu attēlošanai - pogas, virsraksti utt
    |
    */

    'data' => 'Dati',
    'data_hint' => 'Darbības ar datiem',
    'reload' => 'Pārlādēt',
    'new'     => 'Jauns',   
    'filter_hint' => 'Atlasīt reģistra datus pēc filtrēšanas kritērijiem',
    'filter' => 'Filtrēšana',
    'excel'     => 'Uz Excel',
    'excel_hint' => 'Eksportēt saraksta datus uz Excel',
    'view' => 'Skats',
    'lbl_filter' => 'Filtrs:',
    'lbl_filter_hint' => 'Ierakstu filtrēšanu var veikt vienlaicīgi pēc vairākiem datu laukiem. Laukā jāievada filtrēšanas frāze un jānospiež ENTER.',
    'lbl_marked' => 'Iezīmēti:',
    'menu_mark_all' => 'Iezīmēt visus',
    'menu_delete_marked' => 'Dzēst iezīmētos',
    
    'menu_admin_settings' => 'Iestatījumi',
    
    'paginator_page' => 'Lapa',
    'paginator_from' => 'no',
    
    'menu_view' => 'Skatīt',
    'menu_edit' => 'Rediģēt',
    'menu_delete' => 'Dzēst',
    
    'lbl_actions' => 'Darbības',
    
    'row_count' => 'Ierakstu skaits',    
    'rows' => 'Ieraksti',
    'rows_to' => 'Līdz',
    'rows_from' => 'no',
    
    // Importing logic
    'import' => 'Importēt',
    'import_title' => 'Datu imports no Excel',
    'btn_close' => 'Aizvērt',  
    'btn_start_import' => 'Sākt importu',
    'lbl_file' => 'Excel datne',
    'import_hint' => 'Augšupielādējiet Excel datni, kurā kolonnu nosaukumi sakrīt ar reģistra datu ievades formas lauku nosaukumiem. Kolonnas, kuru nosaukumiem nebūs precīza atbilstība, netiks importētas.',
    'import_date_hint' => "Pieļaujamie datumu formāti: 'dd.mm.yyyy' vai 'yyyy-mm-dd'.",
    'file_hint' => 'Datnei jābūt XLSX vai XLS formātā.',
    'invalid_file' => 'Datus nevar importēt! Lūdzu, norādiet korektu importējamo datni.',
    'invalid_file_format' => "Datus nevar importēt! Lūdzu, norādiet importējamo datni *.xlsx, *.xls, *.csv vai *.zip formātā.",
    'success' => "Datu importa process pabeigts!",
    'count_imported' => 'Importēto ierakstu skaits: ',
    'count_updated' => 'Laboto ierakstu skaits: ',
    'ignored_columns' => 'Uzmanību! Pārbaudiet kolonnu nosaukumus Excel datnē.<br/><br/>Netika importēti dati no sekojošām Excel kolonnām: ',
    
    'msg_marked1' => 'Iezīmējiet vismaz vienu ierakstu, kuru dzēst!',
    'msg_confirm_del1' => 'Vai tiešām dzēst iezīmēto ierakstu?',
    'msg_confirm_del_all' => 'Vai tiešām dzēst %s iezīmētos ierakstus?',
    'nothing_imported' => 'Netika importēts vai labots neviens ieraksts.',
    
    'view_editor_form_title' => 'Skats',
    'ch_is_default' => 'Ir noklusētais', //Is default
    'ch_is_for_me' => 'Pieejams tikai man', //Is only for me
    'lbl_view_title' => 'Skata nosaukums',//View title
    'lbl_available' => 'Pieejamie lauki', //Available fields
    'lbl_used' => 'Izmantotie lauki',// Used fields
    'btn_remove_fld' => 'Noņemt',
    'btn_add_fld' => 'Pievienot',
    'lbl_search' => 'Meklēt...',
    'badge_new' => 'Jauns',
    'badge_edit' => 'Rediģēšana',
    'title_copy' => "kopija",
    'lbl_public' => 'Publiskie skati',
    'lbl_private' => "Mani skati",
    'confirm_delete' => 'Vai tiešām dzēst skatu?',
    'tooltip_filter' => 'Filtrs',
    'tooltip_hidden' => 'Neredzams',
    
    'field_settings_form_title' => 'Lauka iestatījumi',
    'lbl_field_title' => 'Lauks',
    'ch_is_hidden' => 'Neredzams',
    'lbl_field_operation' => 'Filtrēšanas kritērijs',
    'lbl_criteria_title' => 'Filtrēšanas vērtība',
    'error_filter_must_be_set' =>"Nav uzstādīta filtrēšanas vērtība!",
    
    'sort_asc' => 'Kārtot augoši',
    'sort_desc' => 'Kārtot dilstoši',
    'sort_off' => 'Nav kārtošana',
    
];