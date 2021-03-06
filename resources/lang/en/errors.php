<?php

/*
|--------------------------------------------------------------------------
| Errors messages
|--------------------------------------------------------------------------
*/
return [
    
    // User authorization exceptions
    'user_is_blocked' => 'The user is blocked!',
    'wrong_current_password' => "Invalid user's current password!",
    'wrong_user_or_password' => 'Invalid username/passsword!',
    
    // %s - attempts count
    'login_attempts_exceeded' => 'Invalid password was entered more than %s times in a row! The user is blocked.',
    
    // 1. %s - attempts count, 2. %s - temporary blocking minutes
    'login_attempts_warning_minutes' => 'Invalid password was entered more than %s times in a row! Perform re-authorization in %s minutes.',
    
    // 1. %s - attempts count, 2. %s - seconds need to wait till unblock
    'login_attempts_warning_seconds' => 'Invalid password was entered more than %s times in a row! The user is temporarily blocked. Perform re-authorization in %s minutes.',
    
    'missing_auth_method' => 'No authentication methods are specified in the system. Authentication is closed.',
    
    // Grid errors
    
    // %s - date format
    'wrong_date_format' => 'The date column can be filtered only by the correct date! The date must be in %s format!',
    
    // Form errors
    'required_field' => "The data cannot be saved! The field '%s' value must be indicated!",
    
    // 1. %s - file extension, 2. %s - file name
    'unsuported_file_extension' => "Unsupported file extension '%s'! The file '%s' cannot be saved.",
    'unsuported_image_file' => "Unsupported picture file extension '%s'! The file '%s' cannot be saved.",
    
     // %s - minimum password characters count
    'min_password' => "The data cannot be saved! Password must have at least %s characters!",
    
    'must_be_uniq' => "The data cannot be saved! The register entries must be unique!",
    
    'nothing_changed' => 'No data changes to save!',
    
    'cant_delete' => "The entry cannot be deleted, because it is used in or referenced by other registers!",
    
    'no_rights_on_register' => "You do not have the necessary rights in this register!",
    'no_rights_to_insert' => "You have no rights to insert a new entry in this register!", 
    'no_rights_to_edit' => "You have no rights to edit the entry in this register!", 
    'no_rights_to_delete' => 'You have no rights to delete the entry in this register!',
    
    'cant_create_folder' => "It is impossible to create a folder '%s' on the server disk!",
    
    'import_wrong_bool' => "Invalid value '%s' provided in the importable Excel file in Yes/No field '%s'! The field can only have values '%s' or '%s'.",
    
    // here and further needs to revalidate translation (2016-10-04)
    'import_wrong_date' => "In the Excel file date field '%s' provided wrong value '%s'! Dates must be in format '%s' or '%s'.",
    'import_wrong_email' => "In the Excel file email field ':field' provided wrong value ':val'! E-mail must be in format like 'person@domain.com'.",
    
    'excel_row' => 'Some rows were not imported because of duplicate values. The skipped Excel row numbers: ',
    
    'no_rights_to_insert_imp' => "You have no rights to insert or import a new entry in the register '%s'!", 
    
    'excel_dependent' => 'Some rows were not imported because of dependency issue! The skipped Excel row numbers: ',
    
    'first_save_for_related' => 'First save the record and then operate with the related items!',
    
    'import_file_not_provided' => 'File to be imported is not provided!',
    
    'import_zip_not_correct' => "The uploaded ZIP file '%s' can't be processed!",
    
    'import_zip_no_data' => "ZIP archive '%s' does not have any Excel or CSV file with the name according to ZIP archive!",
    
    'import_zip_several_data' => "ZIP archive contains more than one data files! It's not possible to choose which file should be used as source: '%s' or '%s'.",
    
    'import_zip_file_not_exists' => "ZIP archive does not contains file '%s'!",
    
    'import_zip_file_cant_copy' => "Can't copy file '%s' to the destination folder '%s'!",
    
    'session_ended' => 'User session is ended!',
    
    'cant_edit_in_process' => 'Record can not be edited because it is in workflow process!',
    
    'access_denied_title' => 'Access denied',
    
    'access_denied_msg' => 'You do not have rights to access the view <b>%s</b>!',
    
    'invalid_input_data' => 'Invalid input data!',
    
    // timeoff
    'no_accrual_policy' => 'There is no accrual policy set for this time off type!',
    'unsupported_factory_class' => "Unsupported class object '%s'!",
    'no_joined_date' => 'Date joined is not set for the employee!',
    
    //tasks widget
    'unsupported_task_view' => "Unsupported tasks view code '%s'!",
    
    //file download
    'file_not_found' => "File '%s' not found! Please, contact the IT support!",
    'file_not_set' => 'List item does not have any file attached!',
    'no_donwload_rights' => "You do not have rights on the item with ID %s!",
    'file_record_not_found' => "Item with ID %s not found! Please, contact the IT support!",
    
    'no_represent_field' => "There is not set field representation for the workflow view! Please, contact IT support.",
    
    'no_respo_empl_field' => "There is an monotoring rule defined for the view '%s', but the field provided in roole is not included in the view!",
    
    'duplicate_view_title' => 'Duplicate view title! Please provide another title.',
    
    'cant_delete_default_view' => "The view can't be deleted because there is no default view set! Please set another view as default.",
    
    'employee_name_exception' => "Employee name '%s' can't be splited as first name and last name",
    
    'unsuported_action_code' => "Unsupported form's action code '%s'!",
    
    'wrong_date_format' => "Data can't be saved! Wrong date format for the field '%s'. Date must be in format %s!",
    
    'unknown_error' => "Something went wrong. Please reload page and try again!",
    
    'workflow' => [
        'not_saved' => 'Workflow is not saved',
        'step_not_connected' => 'Workflow contains steps which are not connected to workflow',
        'step_dont_have_child' => "Workflow contains steps which don't have next or last workflow's step",
        'end_point_in_middle' => 'Workflow contains starting or ending points which are in the middle of workflow',
        'multiple_starting_points' => 'Workflow contains multiple starting points',
        'no_starting_points' => 'Workflow does not contain any starting points',
        'no_finish_points' => 'Workflow does not contain any starting points',
    ],
    
    'field_not_found' => "View does not contain field named '%s'!",
    'field_not_found_id' => "View does not contain field with ID %s!",
    
    'item_locked' => "Item is locked and can't be edited! Item was locked at %s by user %s. Please, wait while %s finish editing.",

    'unable_to_rotate_image' => "System error! Unable to rotate image!",
    'unable_to_copy_file' => "System error! Unable to copy file!",
    
    'no_rights_on_reports' => "Report does not exist or You do not have rights to access reports!",
    
    'no_rights_on_meetings' => "Meeting does not exist or You do not have rights to access meeting!",

    'no_rights_on_constructor' => "Register does not exist or You do not have rights to access constructor!",
	
    'phone_format_err' => "Phone number can contain only numbers!",
    
    'lookup_sql_error' => "SQL error for the lookup field '%s'!",
    
    'no_view_config_rights' => "You don't have rights to configure views for this register!",
    
    'form_validation_err' => "Data can't be saved, because there is at least one validation error!",
    
    'no_id_field_in_import_excel' => 'There is no ID column in Excel field which is required to identify records. Please include ID column in Excel file.',
    
    'crypto_regeneration_in_process' => 'Crypted data is in re-crypting process started by :user_name. New entry adding in system is locked.',
    
    'view_must_have_id_field' => 'The ID field is not included in the view as column. Please, include ID field - it can be set as hidden as well.',
    
    'wrong_action_object' => "System configuration error! It is provided wrong activity to the form, because activity is not intended for database table assigned to this form's register.",
    
    'form_in_editing' => "Data form is in editing mode. Please, save data or cancel editing.",
    
    'btn_ok' => 'OK',
    'attention' => 'Attention!',
    'import_wrong_multival' => "The multilevel classifier ':list' does not contain the value ':val'! Please, add value in classifier and then try again.",
    'import_several_multival' => "The multilevel classifier ':list' have several rows ':val'! Please, make classifier values unique and then try again.",
    'import_lookup_several' => "Can't map value for the field ':fld', because classifier ':list' have several records ':term'.",
    'import_lookup_no_field' => "The list ':list' does not have field named ':fld'!",
    
    'not_valid_email' => "Provided email ':email' is not valid! Email must be in format like 'person@domain.com'.",
    
    'publish_validator_not_exists' => "Provided validator code ':code' is not correct!",
    
    'publish_validator_no_group' => "Provided wrong group ID ':id'!",
    
    'no_rights_on_complect' => 'You do not have rights on learning groups complecting functionality!',

    'no_rights_on_organization' => 'You do not have rights on learning groups complecting functionality for provided organization!',

    'no_rights_on_group' => 'You do not have rights on learning groups complecting functionality for provided group!',

    'err_db_msg_title' => 'Data error',

    'err_db_msg_general' => 'Data processing error! Please, contact your IT support.',

    'cant_identify_object' => "Can not identify object by provided table name ':table'! Found objects count is :found.",
    'cant_identify_register' => "Can not identify register by provided table name ':table'! Found registers count is :found.",
    'object_dont_have_history' => "The object ':table' does not have history logic enabled!",
    'object_update_without_where' => "The object ':table' update/delete method called without Where criteria!",
    'object_update_without_id' => "The object ':table' update method called without ID field in the Where criteria!",
    'object_update_without_compare' => "Wrong call of data changes audit method - it must be compared changes before by calling the method compareChanges().",
    'object_update_commit_no_prepare' => "Wrong call of data changes save method - it must be prepared changes before by calling the method update().",
    'object_delete_commit_no_history' => "Wrong call of data deletion method - it must be prepared changes before by calling the method delete()!",

    'doc_gener_in_workflow' => "Record can't be edited, because it is beeing processed by an workflow or is with status Approoved!",
    'doc_gener_no_template' => 'Register does not have any template attached!',
    
    'no_rights_on_custom_page' => "You don't have access rights on functionality ':page'!",
    'custom_page_not_found' => "Page ':url' is not registered in the system pages register or functionality is not activated!",
];
