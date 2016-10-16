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
    
    // Grid errors
    
    // %s - date format
    'wrong_date_format' => 'Datuma kolonu var filtrēt tikai pēc korekta datuma! Datumam jābūt formātā %s!',
    
    // Form errors
    'required_field' => 'Nevar saglabāt datus! Ir obligāti jānorāda lauka "%s" vērtība!',
    
    // 1. %s - file extension, 2. %s - file name
    'unsuported_file_extension' => 'Neatbalstīts datnes paplašinājums "%s"! Nav iespējams saglabāt datni "%s".',
    
    // %s - minimum password characters count
    'min_password' => 'Nevar saglabāt datus! Paroles laukā ir obligāti jānorāda parole ar vismaz %s simboliem!',
    
    'must_be_uniq' => 'Nevar saglabāt datus! Reģistra ierakstiem ir jābūt unikāliem!',
    
    'nothing_changed' => 'Nav  datu izmaiņu, ko saglabāt! Ieraksts nav mainīts.',
    
    'cant_delete' => 'Nevar dzēst ierakstu, jo tas tiek izmantots vai uz to ir atsauce citos reģistros!',
    
    'no_rights_on_register' => 'Jums nav nepieciešamo tiesību šajā reģistrā!',
    'no_rights_to_insert' => 'Jums nav jauna ieraksta veidošanas tiesības šajā reģistrā!', 
    'no_rights_to_edit' => "Jums nav ieraksta labošanas tiesības šajā reģistrā!", 
    
    'cant_create_folder' => "Nav iespējams izveidot katalogu '%s' uz servera diska!",
    'import_wrong_bool' => "Importējamajā Excel datnē Jā/Nē laukā '%s' norādīta nekorekta vērtība '%s'! Laukā pieļaujams norādīt tikai vērtības '%s' vai '%s'.",
    
    'import_wrong_date' => "Importējamajā datuma laukā '%s' norādīta nekorekta vērtība '%s'! Datumam jābūt formātā '%s'.",
    
    'excel_row' => 'Dažas Excel rindas netika importētas, jo dublējās reģistra ieraksti. Dublējošo un neimportēto Excel rindu numuri: ',
    
    'no_rights_to_insert_imp' => "Jums nav jauna ieraksta veidošanas tiesības reģistrā '%s'!", 
    
    'excel_dependent' => 'Dažas Excel rindas netika importētas, jo nevar uzstādīt atbilstības savstarpēji atkarīgiem ierakstiem. Neimportēto Excel rindu numuri: ',
    
    'first_save_for_related' => 'Vispirms saglabājiet formu un tad veiciet saistīto datu ielādēšanu!',
];
