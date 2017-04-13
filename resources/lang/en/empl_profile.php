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
    'tab_salary' => 'Salary',
    'tab_shares' => 'Shares',
    'tab_polygraph' => 'Polygraph',
    
    'qualif_menu' => 'Qualification',
    'confidential_menu' => 'Confidential',
    
    // Subgrid tabs for qualification (must be equal with tab titles defined in CMS form - in database table dx_tabs)
    'tab_lang' => 'Languages',
    'tab_links' => 'Links',
    'tab_educ' => 'Education',
    'tab_cert' => 'Certificates',
    'tab_cv' => 'CVs & other', 
    
    'tab_documents' => 'Documents',
    'tab_timeoff' => 'Time off',
    'tab_notes' => 'Notes',
    'tab_info' => 'Info',
    
    'assets_menu' => 'Assets',
    
    // Subgrid tabs for assets (must be equal with tab titles defined in CMS form - in database table dx_tabs)
    'tab_cards' => 'Corporate cards',
    'tab_devices' => 'Devices',
    
    'direct_reporters' => 'Direct reporters',
    
    'lbl_about' => 'About',    
    'lbl_edit' => 'Edit',
    'lbl_save' => 'Save',
    'lbl_cancel' => 'Cancel',
    
    'avail_left' => 'Left',
    'avail_active' => 'Active',
    'avail_potential' => 'Potential',
    
    'hint_left' => 'Employee has left',
    'hint_active' => 'Employee is at work',
    'hint_potential' => 'The person is in process of hiring',
    
    'lbl_direct_superv' => 'Direct supervisor',
    
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
        'modified' => "Modified",
        'who_can_see' => 'Who can see this note'
    ], 
    
    'timeoff' => [
        'accrued' => 'Accrued',
        'used' => 'Used',
        'balance' => 'Balance',
        'menu_actions' => 'Actions',
        'accrual_policy' => 'Accrual Policy',
        'calculate' => 'Calculate',
        'delete_accrual' => 'Delete calculated',

        'delete_confirm' => 'Are You sure? Delete calculated accrual?',
        
        'history' => 'History',
        'timeoff' => 'Time off',
        'date_interval' => 'Date interval',
        'total_accrued' => 'Total accrued',
        'total_used' => 'Total used',
        'from_date' => 'From date',
        'to_date' => 'To date',
        'date' => 'Date',
        'type' => 'Type',
        'notes' => 'Notes',
        'used_accrued' => 'Used / Accrued (hours)',
        'balance_hours' => 'Balance (hours)',
        'chart' => 'Chart',
        'table' => 'Table',
        'available' => 'available'
    ], 
];
