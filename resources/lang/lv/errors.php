<?php

/*
|--------------------------------------------------------------------------
| Errors messages
|--------------------------------------------------------------------------
*/
return [
    
    // User authorization exceptions
    'user_is_blocked' => 'Lietotājs ir bloķēts!',
    'wrong_current_password' => 'Norādīta nekorekta lietotāja pašreizējā parole!',
    'wrong_user_or_password' => 'Nekorekti ievadīts lietotājvārds/parole!',
    
    // %s - attempts count
    'login_attempts_exceeded' => 'Parole tika nepareizi ievadīta vairāk kā %c reizes pēc kārtas! Lietotājs tiek bloķēts.',
    
    // 1. %s - attempts count, 2. %s - temporary blocking minutes
    'login_attempts_warning_minutes' => 'Parole tika nepareizi ievadīta vairāk kā %c reizes pēc kārtas! Veiciet atkārtotu autorizācijas mēģinājumu pēc %m minūtēm.',
    
    // 1. %s - attempts count, 2. %s - seconds need to wait till unblock
    'login_attempts_warning_seconds' => 'Parole tika nepareizi ievadīta vairāk kā %c reizes pēc kārtas! Lietotājs ir uz laiku bloķēts. Atkārtojiet autorizāciju pēc %s sekundēm.',
    
    'missing_auth_method' => 'Sistēmā nav norādīta neviena autentifikācijas metode. Autentifikācija ir slēgta.',
    
    // Grid errors
    
    // %s - date format
    'wrong_date_format' => 'Datuma kolonu var filtrēt tikai pēc korekta datuma! Datumam jābūt formātā %s!',
    
    // Form errors
    'required_field' => 'Nevar saglabāt datus! Ir obligāti jānorāda lauka "%s" vērtība!',
    
    // 1. %s - file extension, 2. %s - file name
    'unsuported_file_extension' => 'Neatbalstīts datnes paplašinājums "%s"! Nav iespējams saglabāt datni "%s".',
    'unsuported_image_file' => 'Neatbalstīts attēla datnes paplašinājums "%s"! Nav iespējams saglabāt datni "%s".',
    
    // %s - minimum password characters count
    'min_password' => 'Nevar saglabāt datus! Paroles laukā ir obligāti jānorāda parole ar vismaz %s simboliem!',
    
    'must_be_uniq' => 'Nevar saglabāt datus! Reģistra ierakstiem ir jābūt unikāliem!',
    
    'nothing_changed' => 'Nav  datu izmaiņu, ko saglabāt! Ieraksts nav mainīts.',
    
    'cant_delete' => 'Nevar dzēst ierakstu, jo tas tiek izmantots vai uz to ir atsauce citos reģistros!',
    
    'no_rights_on_register' => 'Jums nav nepieciešamo tiesību šajā reģistrā!',
    'no_rights_to_insert' => 'Jums nav jauna ieraksta veidošanas tiesības šajā reģistrā!', 
    'no_rights_to_edit' => "Jums nav ieraksta labošanas tiesības šajā reģistrā!", 
    'no_rights_to_delete' => 'Jums nav dzēšanas tiesību šajā reģistrā!',
    
    'cant_create_folder' => "Nav iespējams izveidot katalogu '%s' uz servera diska!",
    'import_wrong_bool' => "Importējamajā Excel datnē Jā/Nē laukā '%s' norādīta nekorekta vērtība '%s'! Laukā pieļaujams norādīt tikai vērtības '%s' vai '%s'.",
    
    'import_wrong_date' => "Importējamajā datuma laukā '%s' norādīta nekorekta vērtība '%s'! Datumam jābūt formātā '%s' vai '%s'.",
    
    'excel_row' => 'Dažas Excel rindas netika importētas, jo dublējās reģistra ieraksti. Dublējošo un neimportēto Excel rindu numuri: ',
    
    'no_rights_to_insert_imp' => "Jums nav jauna ieraksta veidošanas tiesības reģistrā '%s'!", 
    
    'excel_dependent' => 'Dažas Excel rindas netika importētas, jo nevar uzstādīt atbilstības savstarpēji atkarīgiem ierakstiem. Neimportēto Excel rindu numuri: ',
    
    'first_save_for_related' => 'Vispirms saglabājiet formu un tad veiciet saistīto datu ielādēšanu!',
    
    'import_file_not_provided' => 'Nav norādīta importējamā datne!',
    
    'import_zip_not_correct' => "Nav iespējams apstrādāt augšupielādēto ZIP datni '%s'!",
    
    'import_zip_no_data' => "ZIP arhīvā '%s' nav neviena Excel vai CSV datne ar importējamajiem datiem un nosaukumu atbilstoši arhīva datnei!",
    
    'import_zip_several_data' => "ZIP arhīvā ir vairāk kā viena datu datne! Nav iespējams noteikt, vai datus importēt no datnes '%s' vai no datnes '%s'.",
    
    'import_zip_file_not_exists' => "ZIP arhīvā nav atrodama datne '%s'!",
    
    'import_zip_file_cant_copy' => "Nav iespējams kopēt datni '%s' uz katalogu '%s'!",
    
    'session_ended' => 'Lietotāja sesija ir beigusies!',
    
    'cant_edit_in_process' => 'Ierakstu nav iespējams rediģēt, jo tas atrodas darbplūsmā!',
    
    'access_denied_title' => 'Piekļuve liegta',
    
    'access_denied_msg' => 'Jums nav piepieciešamo tiesību, lai piekļūtu skata <b>%s</b> datiem!',    
    
    'invalid_input_data' => 'Nepareizi ievadīti dati!',
        
    //timeoff
    'no_accrual_policy' => 'Darbiniekam nav iestatīta atbilstoša kompensējamā laika uzkrāšanas politika',
    'unsupported_factory_class' => "Neatbalstīts klases objekts '%s'!",
    'no_joined_date' => 'Darbiniekam nav uzstādīts darba attiecību sākšanas datums!',
    
    //tasks widget
    'unsupported_task_view' => "Neatbalstīts uzdevmu skata kods '%s'!",
    
    //file download
    'file_not_found' => "Datne '%s' nav atrodama! Lūdzu, sazinieties ar sistēmas uzturētāju!",
    'file_not_set' => 'Ierakstam nav pievienota neviena datne!',
    'no_donwload_rights' => "Jums nav tiesību uz datni ierakstam ar ID %s!",
    'file_record_not_found' => "Datne ar ieraksta ID %s nav atrodama! Lūdzu, sazinieties ar sistēmas uzturētāju!",
    
    'no_represent_field' => "Darbplūsmas skatam nav norādīts, kurus dokumenta laukus attēlot uzdevumā! Sazinieties ar sistēmas uzturētāju.",
    
    'no_respo_empl_field' => "Skatam '%s' definēts kontroles nosacījums, bet nosacījumā norādītais atbildīgā darbinieka lauks nav ietverts skatā!",
    
    'duplicate_view_title' => 'Dublējas skata nosaukums! Lūdzu, norādiet citu skata nosaukumu.',
    
    'cant_delete_default_view' => "Skatu nav iespējams dzēst, jo nav norādīts neviens cits noklusētais skats! Lūdzu, vispirms norādiet citu skatu kā noklusēto.",
    
    'employee_name_exception' => "Darbinieka nosaukumu '%s' nav iespējams sadalīt kā vārdu un uzvārdu",
    
    'unsuported_action_code' => "Neatbalstīts formas aktivitātes kods '%s'!",
    
    'helpdesk_responsible_not_set' => "Nav uzstādīta IT atbalsta pieteikuma veida atbildīgā persona! Ir jānorāda %s pieteikuma veidam '%s'.",
    
    'wrong_date_format' => "Nevar saglabāt datus! Nekorekts datuma formāts laukam '%s'! Datumam jābūt formātā %s!",
    
    'workflow' => [
        'not_saved' => 'Darbplūsma netika saglabta',
        'step_not_connected' => 'Darbplūsma satur soļus, kas nav sasaistīti ar darbplūsmu',
        'step_dont_have_child' => "Darbplūsma satur soļus, kuriem nav nākošā vai noslēdzošā darbplūsmas soļa",
        'end_point_in_middle' => 'Darbplūsma satur sākuma vai beigu punktus, kas atrodas darbplūsmas vidū',
        'multiple_starting_points' => 'Darbplūsma satur vairākus sākuma punktus',
        'no_starting_points' => 'Darbplūsma nesatur nevienu sākuma punktu',
        'no_finish_points' => 'Darbplūsma nesatur nevienu beigu punktu',
    ],
    
    'field_not_found' => "Skatam nav definēts lauks ar nosaukumu '%s'!",
    'field_not_found_id' => "Skatam nav definēts lauks ar ID %s!",
    
    'item_locked' => 'Ieraksts ir bloķēts un rediģēšana nav iespējama! Ierakstu %s sāka rediģēt lietotājs %s. Jums ir jāuzgaida, kamēr %s pabeigs ieraksta rediģēšanu.',

    'unable_to_rotate_image' => "Sistēmas kļūda! Nav iepsējams rotēt datni!",
    'unable_to_copy_file' => "Sistēmas kļūda! Nav iepsējams kopēt datni!",
    
    'no_rights_on_reports' => "Atskaite nav atrodama vai arī Jums nav tiesību piekļūt atskaitei!",
    
    'no_rights_on_meetings' => "Sapulce nav atrodama vai arī Jums nav tiesību piekļūt sapulcei!",
    
    'phone_format_err' => "Tālruņa numurs var sastāvēt tikai no cipariem!",
];
