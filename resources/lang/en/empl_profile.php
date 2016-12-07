<?php

/*
|--------------------------------------------------------------------------
| Labels for employee profile
|--------------------------------------------------------------------------
*/
return [
    
    'err_no_edit_rights' => 'You have no rights to edit profile data!',
    'err_first_save_new_item' => 'Please, save the new employee data first and then add related information.',
    'hint_view_profile' => 'View profile',
    
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
        'new_doc' => 'New document',
        'clear_doc' => 'Clear document data',
        'save_docs' => 'Save documents',
        'country' => 'Country',
        'personal_doc_type' => 'Document type',
        'doc_nr' => 'Document number',
        'valid_to' => 'Valid to',
        'publisher' => 'Publisher',
        'file' => 'File',
    ],    
    
    'notes' => [
        'type_hint' => 'Type a note here...',
        'delete_note_title' => 'Delete note',
        'delete_note_text' => 'Do You really want to delete note?',
        'note_missing' => "Note doesn't exist!",
        'modified' => "Modified"
    ], 
    
    'timeoff' => [
        'menu_actions' => 'Actions',
        'accrual_policy' => 'Accrual Policy',
        'calculate' => 'Calculate',
        'delete_accrual' => 'Delete calculated',

        'delete_confirm' => 'Are You sure? Delete calculated accrual?',
    ], 
];
