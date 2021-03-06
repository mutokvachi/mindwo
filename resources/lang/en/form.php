<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Form's labels
    |--------------------------------------------------------------------------
    */
    
    'excel'     => 'To Excel',
    'excel_hint' => 'Export list data to Excel',
    'view' => 'View',
    'btn_edit' => 'Edit',
    'btn_delete' => 'Delete',
    'btn_copy' => 'Copy',
    'btn_save_as' => 'Save as',
    'btn_pdf' => 'To PDF',
    'btn_start_workflow' => 'Start workflow',
    'btn_info' => 'Inform',
    'btn_info_hint' => 'Employees which receives document as informative task',
    'btn_save' => 'Save',
    'btn_close' => 'Close',
    'btn_cancel' => 'Cancel',
    'btn_accept' => 'Accept',
    'btn_yes' => 'Yes',
    'btn_no' => 'No',
    'modal_confirm_title' => 'Confirmation',
    'modal_confirm_body' => 'Do you want to confirm the action?',
    'err_value_not_set' => 'Value not set!',
    'badge_edit' => 'Editing',
    'badge_new' => 'New',
    
    'lbl_full_screen' => 'Is full screen mode',
    
    // To PDF
    'lbl_data' => 'Data',
    'lbl_id' => 'Record ID',
    'lbl_print_date' => 'Data as of',
    
    'link_history' => 'History',
    'hint_history' => 'View item changes history',
    'hint_settings' => 'Open form settings',
    'history_form_title' => 'History',
    'btn_changes' => 'See changes',
    
    'history_not_found' => 'This item does not have any changes history record.',

    'chat' => [
        'db' => [
            'list' => 'Register',
            'item' => 'Item',
            'message' => 'Message',
            'file_name' => 'File name',
            'user' => 'User',
            'chat_msgs' => 'Chat messages'
        ],
        'users' => 'Users',
        'btn_add_user' => 'Add users',
        'btn_del_user' => 'Remove',
        'btn_send_msg' => 'Send message',
        'btn_send_file' => 'Send attachment',
        'btn_download' => 'Download',
        'btn_try_again' => 'Try again',
        'e_data_not_retrieved' => 'Server error while retrieving data.',
        'title_confirm_del_user' => 'Remove user from chat',
        'description_confirm_del_user' => 'Do you really want to remove user from chat?',
        'e_user_not_removed' => 'Error while removing user from chat',
        'i_user_removed' => 'User removed from chat',
        'e_user_not_specified' => 'User is not specified',
        'e_user_not_added' => 'Error while adding user to chat',
        'i_user_added' => 'User successfully added to chat',
        'e_user_exist' => 'User is already in the chat',
        'chat' => 'Chat',
        'type_hint' => 'Type a message here...',
        'note_missing' => "Note doesn't exist!",
        'modified' => "Modified",
        'e_msg_not_saved' => 'Error while saving message. Please try to send message again!',
        'e_file_not_saved' => 'Error while saving attachment. Please try to send attachment again!',
        'e_file_to_large' => 'Error while saving attachment. File is too large.',
        'e_no_users' => 'No users in this chat',  
        'i_upload_progress' => 'Uploading attachments...',      
        'task_chat_description' => 'You have been added in discussion about document.',
    ],

    'template' => [
        'popup_title' => 'Choose template',
        'intro' => 'Register has several templates attached. Please, choose template which will be used for generation.',
        'download' => 'Download template',
        'doc_generate_btn' => 'Generate',
        'generate_hint' => 'Generate document file from template',
        'choose_btn' => 'Generate',
        'generation_ok' => 'File successfully generated!',
        'btn_manage_templ' => 'Manage templates',
    ],
];