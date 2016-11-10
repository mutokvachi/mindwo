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
    
     // %s - minimum password characters count
    'min_password' => "The data cannot be saved! Password must have at least %s characters!",
    
    'must_be_uniq' => "The data cannot be saved! The register entries must be unique!",
    
    'nothing_changed' => 'No data changes to save!',
    
    'cant_delete' => "The entry cannot be deleted, because it is used in or referenced by other registers!",
    
    'no_rights_on_register' => "You do not have the necessary rights in this register!",
    'no_rights_to_insert' => "You have no rights to insert a new entry in this register!", 
    'no_rights_to_edit' => "You have no rights to edit the entry in this register!", 
    
    'cant_create_folder' => "It is impossible to create a folder '%s' on the server disk!",
    
    'import_wrong_bool' => "Invalid value '%s' provided in the importable Excel file in Yes/No field '%s'! The field can only have values '%s' or '%s'.",
    
    // here and further needs to revalidate translation (2016-10-04)
    'import_wrong_date' => "In the Excel file date field '%s' provided wrong value '%s'! Dates must be in format '%s'.",
    
    'excel_row' => 'Some rows were not imported because of duplicate values. The skipped Excel row numbers: ',
    
    'no_rights_to_insert_imp' => "You have no rights to insert a new entry in the register '%s'!", 
    
    'excel_dependent' => 'Some rows were not imported because of dependency issue! The skipped Excel row numbers: ',
    
    'first_save_for_related' => 'First save the record and then operate with the related items!',
    
    'import_file_not_provided' => 'File to be imported is not provided!',
    
    'import_zip_not_correct' => "The uploaded ZIP file '%s' can't be processed!",
    
    'import_zip_no_data' => "ZIP archive '%s' does not have any Excel or CSV file with the name according to ZIP archive!",
    
    'import_zip_several_data' => "ZIP archive contains more than one data files! It's not possible to choose which file should be used as source: '%s' or '%s'.",
    
    'import_zip_file_not_exists' => "ZIP archive does not contains file '%s'!",
    
    'import_zip_file_cant_copy' => "Can't copy file '%s' to the destination folder '%s'!",
    
    'session_ended' => 'User session is ended!',
];
