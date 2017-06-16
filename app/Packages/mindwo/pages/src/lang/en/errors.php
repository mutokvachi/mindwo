<?php

/*
|--------------------------------------------------------------------------
| Errors messages
|--------------------------------------------------------------------------
*/
return [
    
    // %s - usage count. The last word must be singular and without sentence ending mark
    'cant_delete' => "The record can't be deleted, because it is referenced from the object's '%s' registers %s time",
    'plural_ending' => 's',
    'singular_ending' => '',
    
    'no_default_page' => 'There is no default page set in CMS settings! Please, contact the IT support.',
    'access_denied' => 'Access denied',
    'access_denied_msg' => "You do not have rights to access the resource '%s'!",
    
    'view_not_found' => 'Requested view %s not found!',
    'attention' => 'Attention',
    
    'no_rights_on_register' => "You do not have the necessary rights in this register!",
    'no_rights_to_delete' => 'You have no rights to delete the entry in this register!',
    
    // 1st %s - register name, 2nd %s - record count
    'cant_delete_used_record' => "Can't delete record, because it is used in the related register '%s' %s times.",
    'cant_delete_record_because_task' => "Can't delete record, because it is used in an workflow and there is at least one task depending on this record.",
    'task_list' => 'Tasks',
    
    "lookup_view_error" => "The view used for lookup values does not contain the table '%s' field '%s'! Data load is not possible.",
];
