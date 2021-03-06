<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Tasks form labels
    |--------------------------------------------------------------------------
    */
    'form_title' => 'Uzdevums',
    'lbl_register' => 'Dokumenta reģistrs',
    'lbl_reg_nr' => 'Dokumenta reģ. nr.',
    'hint_open_doc' => 'Skatīt dokumenta kartiņu',
    'btn_open_doc' => 'Skatīt',
    'lbl_about' => 'Saturs',
    'lbl_task' => 'Uzdevums',
    'lbl_task_details' => 'Uzdevuma paskaidrojums',
    'lbl_task_creator' => 'Uzdevējs',
    'lbl_substitute_info' => 'Aizvietošanas informācija',
    'lbl_task_created' => 'Uzdots',
    'lbl_task_performer' => 'Izpildītājs',
    'lbl_due_date' => 'Izpildes termiņš',
    'lbl_status' => 'Statuss',
    'lbl_finished_date' => 'Pabeigts',
    'lbl_comment' => 'Komentārs',
    'btn_read' => 'Iepazinos',
    'btn_done' => 'Izpildīt',
    'btn_delegate' => 'Deleģēt',
    'btn_reject' => 'Noraidīt',
    'btn_close' => 'Aizvērt',
    
    'status_delegated' => 'Deleģēts',
    'status_done' => 'Izpildīts',
    'status_rejected' => 'Noraidīts',
    
    'doc_in_process' => 'Procesā',
    'doc_approved' => 'Apstiprināts',
    'doc_rejected' => 'Noraidīts',
    
    'comment_compleated' => 'Izpildīts automātiski, jo visi deleģētie uzdevumi ir izpildīti.',
    
    // %s - employee name
    'comment_anulated' => 'Uzdevumu anulēja %s!',
    
    'comment_somebody_rejected' => 'Kāds no citiem saskaņotājiem veica noraidīšanu!',
    
    // %s - employee name
    'comment_rejected' => 'Uzdevums anulēts, jo %s veica noraidīšanu!',
    
    'notify_task_delegated' => 'Uzdevums veiksmīgi deleģēts!',
    'notify_err_provide_employee' => 'Jānorāda darbinieks, kuram tiks deleģēts uzdevums!',
    'notify_err_provide_description' => 'Jānorāda uzdevuma apraksts!',
    'notify_saved' => 'Uzdevuma dati veiksmīgi saglabāti!',
    
    'congrat_title' => 'Apsveicam!',
    'congrat_all_done' => 'Labs darbs - jums visi uzdevumi ir izpildīti.',
    
    'err_value_not_set' => 'Nav norādīta vērtība!',
    'err_date_format' => 'Nevar saglabāt datus! Nekorekts termiņa datuma formāts! Termiņam jābūt formātā %s!',
    'err_date_delegate' => 'Nevar saglabāt datus! Deleģētā uzdevuma terminš nevar būt lielāks par galvenā uzdevuma termiņu %s.',
    'err_subst_delegate' => 'Uzdevumu nevar deleģēt, jo ir darbinieku aizvietošana un aizvietošanas rezultātā uzdevums jāizpilda Jums. %s.',
    'err_rights_exists' => 'Jums jau ir piekļuve dokumentam - informācijas uzdevums netiks izveidots!',
    'err_no_list_rights' => 'Jums nav nepieciešamo tiesību šajā reģistrā!',
    'err_allready_informed' => 'Norādītajam darbiniekam dokuments jau ir nodots informācijai!',
    'err_comment_required' => 'Noraidīšanas gadījumā ir obligāti jānorāda komentārs!',
    'err_no_workflow' => 'Reģistram nav definēta neviena aktīva darbplūsma!',
    'err_no_wf_step' => 'Darbplūsmai nav nodefinēts neviens solis! Sazinieties ar sistēmas uzturētāju.',
    
    // %s - field name
    'err_approve_field' => "Lai saskaņotu, vispirms ir jānorāda obligātais saskaņojamā dokumenta lauks '%s'!",
    
    // %s - field name
    'err_approve_field_num' => "Lai saskaņotu, vispirms ir jānorāda obligātā saskaņojamā dokumenta lauka '%s' skaitliskā vērtība, kurai jābūt lielākai par 0!",
    
    'err_approove_field_lookup' => "Lai saskaņotu, vispirms ir jānorāda obligātā saskaņojamā dokumenta lauka '%s' vērtība (saistītais ieraksts)!",
    'err_approve_lookup_approved' => "Lai saskaņotu, saskaņojamā dokumenta laukā '%s' norādītajam saistītajam ierakstam ir jābūt ar statusu 'Apstiprināts'!",
    
    'err_cant_edit_task' => 'Nav pieļaujams rediģēt/deleģēt pabeigtu uzdevumu vai uzdevumu, kas uzdots citam darbiniekam!',
    'err_no_paralel_step_task' => 'Darbplūsmas paralēlajam solim nav izveidots uzdevums! Sazinieties ar sistēmas uzturētāju.',
    'err_wrong_wf_definition' => 'Nekorekti definēta darbplūsma! Paralēli drīkst būt tikai saskaņošanas vai iepazīšanās soļi. Sazinieties ar sistēmas uzturētāju.',
    'err_wrong_yes_settings' => "Nekorekti definēta darbplūsma! Paralēlo soļu 'Jā' vērtību uzstādījumiem jābūt vienādiem. Sazinieties ar sistēmas uzturētāju.",
    'err_wrong_no_settings' => "Nekorekti definēta darbplūsma! Paralēlo soļu 'Nē' vērtību uzstādījumiem jābūt vienādiem. Sazinieties ar sistēmas uzturētāju.",
    'err_infinite_loop' => 'Darbplūsmai ir pārāk liels iterāciju skaits (%s)! Sazinieties ar sistēmas uzturētāju.',
    
    // 1st %s - list ID, 2nd %s - task type id, 
    'err_wrong_task_type' => 'Reģistra (ID = %s) darbplūsmai norādīts neatbalstīts uzdevuma veids (%s)! Sazinieties ar sistēmas uzturētāju."',
    // 1st %s - list ID, 2nd %s - step nr 
    'err_step_not_exists' => 'Reģistra (ID = %s) darbplūsmai norādīts neeksistējošs solis (%s)! Sazinieties ar sistēmas uzturētāju.',
    // %s - operation ID
    'err_wrong_operation' => 'Darbplūsmai ir norādīts neatbalstīts lauka operācijas veids (%s)! Sazinieties ar sistēmas uzturētāju.',
    
    'msg_workflow_startet' => 'Darbplūsmas process veiksmīgi uzsākts!',
    
    'err_provide_approver' => 'Lūdzu, norādiet vismaz vienu saskaņotāju!',
    'err_first_save_to_init' => 'Lai uzsāktu darbplūsmu, vispirms veiciet datu saglabāšanu!',
    'err_first_save_to_info' => 'Lai dokumentu nodotu informācijai, vispirms tas ir jāsaglabā!',
    
    'menu_task_history' => 'Uzdevumu vēsture',
    'menu_cancel_wf' => 'Atcelt darbplūsmu',
    
    'history_title' => 'Darbplūsmas uzdevumu vēsture',
    
    'lbl_rejected_by' => 'Noraidīja',
    'lbl_started_workflow' => 'Uzsāka darbplūsmu',
    'lbl_stoped_workflow' => 'Atcelta darbplūsma',
    
    'btn_cancel_wf' => 'Atcelt darbplūsmu',
    'lbl_cancel_comment' => 'Komentārs',
    'msg_wf_canceled' => 'Darbplūsma ir atcelta!',
    'err_comment_required_to_cancel' => 'Lai atceltu darbplūsmu, ir obligāri jānorāda komentārs!',
    'wf_cancel_form_title' => 'Darbplūsmas atcelšana',
    
    'err_wf_no_rights_to_cancel' => 'Jums nav nepieciešamo tiesību atcelt šo darbplūsmu!',
    'err_wf_not_in_process' => 'Atcelt var tikai procesā esošas darbplūsmas!',
    
    // tasks notify popup
    'lbl_notify_start' => 'Jums ir jāizpilda',
    'lbl_tasks_1' => 'uzdevums',
    'lbl_tasks_n' => 'uzdevumi',
    'lbl_goto_start' => 'Dodieties uz',
    'lbl_goto_link_title' => 'uzdevumu sadaļu',
    'lbl_goto_end' => 'un izpildiet uzdevumus.',
    
    'err_allready_delegated' => 'Uzdevums jau ir deleģēts norādītajam darbiniekam vai tā aizvietotājam!',
    'info_no_delegated_tasks' => 'Nav neviena deleģētā uzdevuma.',
    
    'tab_new_deleg_task' => 'Jauns deleģētais uzdevums',
    'tab_list_deleg_tasks' => 'Deleģētie uzdevumi',
    'revoke_hint' => 'Atcelt deleģēto uzdevumu un visus tam pakārtotos uzdevumus.',
    'msg_confirm_delegate_revoke' => 'Vai tiešām atcelt deleģēto uzdevumu?',
    
    'confirm_yes' => 'Jā',
    'confirm_no' => 'Nē',
    'err_task_not_found' => 'Uzdevums ar ID %s nav atrasts!',
    'notify_task_canceled' => 'Uzdevums ir atcelts.',
    
];