﻿<?php

/*
|--------------------------------------------------------------------------
| Labels for workflows related UI
|--------------------------------------------------------------------------
*/

return [

    // Workflow init form title
    'wf_init_form_title' => 'Darbplūsmas uzsākšana',
    
    // Workflow init cancel button
    'wf_init_btn_cancel' => 'Atcelt',
    
    // Workflow init start button
    'wf_init_btn_start' => 'Sākt darbplūsmu',
    
    // It is not defined possibility to setup approvers before workflow init
    'wf_init_err_not_enabled' => 'Reģistra darbplūsmai nav norādīta iespēja iestatīt saskaņotājus!',
    
    // Due label title
    'wf_init_due_label' => 'Termiņš',
    
    // Due label title
    'wf_init_remove_approver_btn' => 'Noņemt',
    
    // Due label days title
    'wf_init_due_days' => 'diena(s)',
    
    // Hint label for ordering
    'wf_init_hint_label' => 'Kā mainīt secību',
    
    // Hint answer for ordering
    'wf_init_hint_answer' => 'Nospiediet ar peli uz rindas sākumā esošo pelēko pogu un velciet uz augšu vai leju',
    
    // Add approver button caption
    'wf_init_btn_add_approver' => 'Pievienot saskaņotāju',
    
    // Approval order - paralel
    'wf_init_approval_paralel' => 'Saskaņot paralēli',
    
    // Approval order - sequence
    'wf_init_approval_sequence' => 'Saskaņot secīgi',
    
    // Substitute title for hint
    'wf_init_substit_title' => 'Aizvietošana',
    
    // Title for sub-form for approver adding
    'wf_init_approver_form_title' => 'Saskaņotāja pievienošana',
    
    // Title for approver add button
    'wf_init_approver_btn_add' => 'Pievienot',
    
    // Title for employee lookup field in approvers adding form
    'wf_init_add_form_employee_label' => 'Darbinieks',
    
    // Hint for employee lookup field in approvers adding form
    'wf_init_add_form_employee_label_hint' => 'Ir iespējams pievienot darbiniekus, kuriem ir izveidots lietotājs MEDUS sistēmā un kuri strādā uzņēmumā (nav atbrīvoti).',
    
    // Success message on added approver
    'wf_init_add_form_employee_success' => 'Saskaņotājs veiksmīgi pievienots!',
    
    // Placeholder for employee search field
    'wf_init_add_form_employee_search_placeholder' => 'Meklēt darbinieku...',
    
    // Message for system error if search not working
    'wf_init_add_form_employee_system_error' => 'Sistēmas kļūda - nav iespējams ielādēt datus!',
    
    // Message when employee is not set but add button pressed
    'wf_init_add_form_employee_error_not_set' => 'Lūdzu, norādiet darbinieku!',
    
    // Message when try to add already added employee
    'wf_init_add_form_employee_error_already_added' => 'Saskaņotājs jau ir pievienots!',
    
    // Placeholder for position title
    'wf_init_add_form_employee_position_placeholder' => 'Amats',
    
    'wf_init_approvers_title' => 'Saskaņotāju iestatīšana',
    
    'wf_init_aproovers_hint' => 'Ja nepieciešams, pirms darbplūsmas uzsākšanas variet koriģēt saskaņotāju secību, noņemt saskaņotāju vai pievienot vēl citus saskaņotājus.',
    
    'err_no_direct_manager' => "The workflow can not be started because the employee '%s' do not have dirrect manager!",
    
    'err_unsuported_activity' => 'Unsupported custom workflow activity code %s!',
    
    'fld_activity' => 'Activity',
    
    'performer_system' => 'System',
    'performer_empl' => 'Employee',
    'err_no_substitute' => "Employee '%s' is absent and the substitute person is not provided!",

    'yes' => 'Yes',
    'no' => 'No',
    'success' => 'Workflow saved',
    'wf_details' => "Workflow details",
    'wf_steps' => "Workflow steps",
    'success_arrange' => 'Workflow successfully arranged!',
    
    'save' => 'Save',
    'arrange' => 'Arrange automatically',
    'arrange_text' => "Do You want to arrange workflow's steps automatically?",
    'arrangee_tooltip' => "Arranging workflow's steps automatically will delete all visual positionioning changes made by user",
    'form_title' => 'Workflow',
    'list' => 'Register',
    'title' => 'Name',
    'description' => 'Description',
    'is_custom_approve' => 'Is custom approve',
    'valid_from' => 'Valid from',
    'valid_to' => 'Valid to',
     'must_save_title' => 'Save workflow',
    'must_save_text' => "Workflow must be saved before editting wokrflow's steps. Do You want to save workflow?",
    'delete_confirm_title' => 'Delete element',
    'delete_confirm_text' => "Do You want to delete this workflow's element?",
    'lbl_deny' => 'DENIED',
    'open_designer' => 'Open visual designer',
    'err_cant_get_unit_leader' => "There is not possible to get unit leader for the employee '%s'!",
    
    'cant_delegate_to_myself' => "Task can't be delegated to yourself! Please, provide another employee.",
    
    'alowed_delegate_only_subordinated' => 'Task can be delegated only to direct subordinates!',
];