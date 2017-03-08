<?php

/*
|--------------------------------------------------------------------------
| Labels for informative task creation form
|--------------------------------------------------------------------------
*/

return [

    // Form title
    'form_title' => 'Document information transfer',
    
    // Close button
    'btn_close' => 'Close',
    
    // Make info task button
    'btn_send' => 'Inform',  
    
    // Label for employee field
    'lbl_employee' => 'Employee',
    
    // Label for info field
    'lbl_task_info' => 'Comment',
    
    // Hint for employee field
    'hint_employee' => 'It is possible to inform only active (non terminated) employees and which have user account in this system.',
    
    // Success message on added task
    'msg_success' => 'Informative task created successfully!',
    
    // Placeholder for employee search field
    'plh_search' => 'Search employee...',
    
    // Message for system error if search not working
    'err_system_error' => 'System error - data load is not possible!',
    
    // Message when employee is not set but add button pressed
    'err_empl_not_set' => 'Please, set employee!',
    
    // Placeholder for position title
    'plh_position' => 'Position',
    
    'lbl_inform_count' => 'Informed',
    
    'msg_no_inform' => 'Document has not yet been transfered to any employee',
    
    'lbl_have_read' => 'Acknowledge',
    
    'msg_done' => 'Document has been transfered to',
    'msg_done_end_n' => 'employees',
    'msg_done_end_1' => 'employee',
    
    'lbl_role' => 'Role (employees group)',
    'hint_role' => 'It is possible to make informative tasks for all employees from particular role.',
    'err_nothing_done' => 'No new emloyee was informed!',
    'hint_info_form' => 'You can transfer this document to an employee or employee group (role).',
];