<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Tasks form labels
    |--------------------------------------------------------------------------
    */
    'form_title' => 'Task',
    'lbl_register' => 'Register',
    'lbl_reg_nr' => 'Item ID',
    'hint_open_doc' => 'View item',
    'btn_open_doc' => 'View',
    'lbl_about' => 'About',
    'lbl_task' => 'Task',
    'lbl_task_details' => 'Task details',
    'lbl_task_creator' => 'Task assigner',
    'lbl_substitute_info' => 'Substitution information',
    'lbl_task_created' => 'Assigned',
    'lbl_task_performer' => 'Peformer',
    'lbl_due_date' => 'Due date',
    'lbl_status' => 'Status',
    'lbl_finished_date' => 'Compleated date',
    'lbl_comment' => 'Comment',
    'btn_read' => 'Read',
    'btn_done' => 'Approve',
    'btn_delegate' => 'Delegate',
    'btn_reject' => 'Reject',
    'btn_close' => 'Close',
    
    'status_delegated' => 'Delegated',
    'status_done' => 'Approved',
    'status_rejected' => 'Rejected',
    
    'doc_in_process' => 'In process',
    'doc_approved' => 'Approved',
    'doc_rejected' => 'Rejected',
    
    'comment_compleated' => 'Done automatically, because all delegated tasks were compleated.',
    
    // %s - employee name
    'comment_anulated' => 'Task canceled by %s!',
    
    'comment_somebody_rejected' => 'Some of other approvers rejected!',
    
    // %s - employee name
    'comment_rejected' => 'Task is canceled, because %s rejected!',
    
    'notify_task_delegated' => 'Task delegated sucessfully!',
    'notify_err_provide_employee' => 'Please, provide employee to whom to delegate!',
    'notify_err_provide_description' => 'Please, provide task description!',
    'notify_saved' => 'Task saved sucessfully!',
    
    'congrat_title' => 'Congratulations!',
    'congrat_all_done' => 'Well done - all tasks compleated.',
    
    'err_value_not_set' => 'Value not provided!',
    'err_date_format' => 'Data can not be saved! Wrong due date format! Date must be in format %s!',
    'err_date_delegate' => 'Data can not be saved! Due date of the delegated task can not be after the initial task due date (%s).',
    'err_subst_delegate' => 'Task can not be delegated, because of substitution loop which results that task had to be done by yourself. %s.',
    'err_rights_exists' => "You allready have access to the register item! Informative task won't be created!",
    'err_no_list_rights' => "You don't have rights on this register!",
    'err_allready_informed' => 'Employee allready have informative task for this item!',
    'err_comment_required' => 'Explanation is required in case of rejection!',
    'err_no_workflow' => 'Register does not have any active workflow!',
    'err_no_wf_step' => 'Workflow does not have any step defined! Please, contact IT support.',
    
    // %s - field name
    'err_approve_field' => "Please, provide the required value for the field '%s' in order to approve!",
    
    // %s - field name
    'err_approve_field_num' => "Please, provide the required value for the field '%s' in order to approve! Value must me an integer greater than 0.",
    
    'err_approove_field_lookup' => "Please, provide the required value for the field '%s' (set related record) in order to approve!",
    'err_approve_lookup_approved' => "Please, provide the corect value for the field '%s' (set related record must be approved) in order to approve!",
    
    'err_cant_edit_task' => "You don't have rights to edit compleated task or task for another employee.",
    'err_no_paralel_step_task' => 'There are no task for the workflow paralel step. Please, contact IT support.',
    'err_wrong_wf_definition' => 'Wrong workflow definition! Only approval or informative steps may be in paralel. Please, contact IT support.',
    'err_wrong_yes_settings' => "Wrong workflow definition! Positive decision step numbers for paralel steps must be equal. Please, contact IT support.",
    'err_wrong_no_settings' => "Wrong workflow definition! Negative decision step numbers for paralel steps must be equal. Please, contact IT support.",
    'err_infinite_loop' => 'To many iterations (%s) for the workflow. Please, contact IT support.',
    
    // 1st %s - list ID, 2nd %s - task type id, 
    'err_wrong_task_type' => "Wrong task type '%s' for the register (ID = %s) workflow! Please, contact IT support.",
    // 1st %s - list ID, 2nd %s - step nr 
    'err_step_not_exists' => 'Step with nr. %s does not exists for the register (ID = %s) workflow! Please, contact IT support.',
    // %s - operation ID
    'err_wrong_operation' => "Wrong operation type '%s' for the register workflow! Please, contact IT support.",
    
    'msg_workflow_startet' => 'Workflow process started sucessfully!',
        
    'err_provide_approver' => 'Please, provide at least one approver!',
    'err_first_save_to_init' => 'Save item in order to start the workflow!',
    'err_first_save_to_info' => 'Save item in order to make informative task!',
    
    'menu_task_history' => 'Workflow history',
    'menu_cancel_wf' => 'Cancel workflow',
    
    'history_title' => 'Workflow history',
];