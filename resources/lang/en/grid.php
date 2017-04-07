<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Lists labels
    |--------------------------------------------------------------------------
    */
    
    'data' => 'Data',
    'data_hint' => 'Operations with data',
    'reload' => 'Reload',
    'new'     => 'New',
    'filter_hint' => 'Filter register data by provided criteria',
    'filter' => 'Filter',
    'excel'     => 'To Excel',
    'excel_hint' => 'Export list data to Excel',
    'view' => 'View',
    'lbl_filter' => 'Filter:',
    'lbl_filter_hint' => 'Record filtering can be carried out simultaneously by multiple data fields. Enter a filtering phrase and press ENTER.',
    'lbl_marked' => 'Selected:',
    'menu_mark_all' => 'Select all',
    'menu_delete_marked' => 'Delete selected',
    
    'menu_admin_settings' => 'Register settings',
    'menu_form_settings' => 'Form settings',
    'menu_view_settings' => 'View settings',
    
    'paginator_page' => 'Page',
    'paginator_from' => 'from',
    
    'menu_view' => 'View',
    'menu_edit' => 'Edit',
    'menu_delete' => 'Delete',
    
    'lbl_actions' => 'Actions',
    
    'row_count' => 'Row count',
    'rows' => 'Rows',
    'rows_to' => 'To',
    'rows_from' => 'from',
    
    // Importing logic
    'import' => 'Import',
    'import_title' => 'Data import from Excel',
    'btn_close' => 'Close',
    'btn_start_import' => 'Start import',
    'lbl_file' => 'Excel file',
    'import_hint' => "Upload an Excel file in which the column names and register data input form field names coincide. The columns, whose names do not coincide, will not be imported.", 
    'import_date_hint' => "Supported dates formats: 'dd.mm.yyyy' or 'yyyy-mm-dd'.",
    'file_hint' => 'The file must be in XLSX or XLS file format.',
    'invalid_file' => "The data cannot be imported! Please provide the correct importable Excel file.",
    'invalid_file_format' => "The data cannot be imported! Please provide the importable Excel file in *.xlsx or *.xls file format.",
    'success' => "Data import finished!",
    'count_imported' => 'Imported rows: ',
    'count_updated' => 'Edited rows: ',
    'ignored_columns' => 'Attention! Please check the column names in the Excel file.<br/><br/>No data were imported from the following Excel columns: ',    
    
    'msg_marked1' => 'Select at least one row you want to delete!',
    'msg_confirm_del1' => 'Do you really want to delete the selected row?',
    'msg_confirm_del_all' => 'Do you really want to delete the selected %s rows?',
    'nothing_imported' => 'No record was imported or updated.',
    
    'view_editor_form_title' => 'View',
    'ch_is_default' => 'Is default', //Is default
    'ch_is_for_me' => 'Is only for me', //Is only for me
    'lbl_view_title' => 'View title',//View title
    'lbl_available' => 'Available fields', //Available fields
    'lbl_used' => 'Used fields',// Used fields
    'btn_remove_fld' => 'Remove',
    'btn_add_fld' => 'Add',
    'lbl_search' => 'Search...',
    'badge_new' => 'New',
    'badge_edit' => 'Editing',
    'title_copy' => 'copy',
    'lbl_public' => 'Public views',
    'lbl_private' => "My views",
    'confirm_delete' => 'Do you really want to delete the view?',
    'tooltip_filter' => 'Filter',
    'tooltip_hidden' => 'Hidden',
    
    'field_settings_form_title' => 'Field settings',
    'lbl_field_title' => 'Field',
    'ch_is_hidden' => 'Hidden',
    'lbl_field_operation' => 'Filtering criteria',
    'lbl_criteria_title' => 'Criteria value',
    'error_filter_must_be_set' =>"Filtering value must be set!",
    
    'sort_asc' => 'Sort ascending',
    'sort_desc' => 'Sort descending',
    'sort_off' => 'No sorting',
    
    'report_interval' => 'Date interval',
    'btn_prepare_report' => 'Prepare report',
    
];