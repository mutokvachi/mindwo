<?php
//Rename this file to dx.php for production or development environment
return [
    
    /*
    |--------------------------------------------------------------------------
    | Excel eksporta datnes maksimāli pieļaujamais rindu skaits
    |--------------------------------------------------------------------------
    |
    | Lai nepārslogotu serveri un neizsauktu Timeout kļūdas, ir jākombinē timeout limits un Excel maksimālo rindu skaita limits.
    */
    
    'excel_export_maximum_rows' => 1000,
    
    /*
    |--------------------------------------------------------------------------
    | Ierakstu skaits tabulāro sarakstu 1 lapā
    |--------------------------------------------------------------------------
    |
    | Lai uzlabotu ātrdarbību un nepārslogotu serveri, ir tabulāro sarakstu dati ir jāatgriež pa porcijām (lapām).
    | Ar šo parametru var konfigurēt maksimālo ierakstu skaitu vienā lapā.
    */   
    
    'grid_page_rows_count' => 20,
    
    /*
    |--------------------------------------------------------------------------
    | Tabulārā saraksta HTML elementa tekstuālais idnetifikators
    |--------------------------------------------------------------------------
    |
    | Tabulārie saraksti tiek ielādēti DIV elementā ar norādīto idnetifikatoru. Tas nepieciešams, lai varētu atjaunināt saraksta datus ar AJAX pieprasījumiem nepārlādējot visu lapu.
    */   
    
    'grid_html_element_id' => 'td_data',
    
    /*
    |--------------------------------------------------------------------------
    | Publiskā lietotāja identifikators
    |--------------------------------------------------------------------------
    |
    | Intranet publiskajām sadaļām var piekļūt jebkurš uzņēmuma tīkla lietotājs. Sistēmā ir speciāls lietotājs, kuram var definēt lomas. 
    | Tādā veidā var konfigurēt publiski pieejamos resursus.
    */   
    
    'public_user_id' => 2,
    
    /*
    |--------------------------------------------------------------------------
    | Ierakstu skaits ziņu plūsmas vienā lapas skatījumā
    |--------------------------------------------------------------------------
    |
    | Ziņu plūsmas tiek ielādētas ritinot lapu uz leju pa porcijām. Šis iestatījums paredzēts porcijas ierkastu skaita norādīšanai.
    */   
    
    'feeds_page_rows_count' => 25,

    /*
    |--------------------------------------------------------------------------
    | Ierakstu skaits galeriju un izdevumu lapas skatījumā
    |--------------------------------------------------------------------------
    */   
    
    'gallery_publish_item_count' => 9,
    
    /*
    |--------------------------------------------------------------------------
    | Datuma formāts
    |--------------------------------------------------------------------------
    |
    | Datuma formāts attēlošanai formās un reģistros
    */   
    
    'date_format' => 'dd.mm.yyyy',
    
    /*
    |--------------------------------------------------------------------------
    | Funkciju datuma formāts
    |--------------------------------------------------------------------------
    |
    | Izmantojams formatēšanas funkcijās, lai pārvērstu datumu kā tekstu kā arī attēlošanai datuma laukos
    */ 
    'txt_date_format' => 'd.m.Y',
    
    
    /*
    |--------------------------------------------------------------------------
    | Funkciju datuma/laika formāts
    |--------------------------------------------------------------------------
    |
    | Izmantojams formatēšanas funkcijās, lai pārvērstu datumu un laiku kā tekstu kā arī attēlošanai datuma laukos
    */ 
    'txt_datetime_format' => 'd.m.Y H:i',
    
    /*
    |--------------------------------------------------------------------------
    | Maksimālais iezīmju skaits, ko attēlot pie ziņas/attēla/video
    |--------------------------------------------------------------------------
    |
    | Ar šo parametru ir iespējams ierobežot attēlojamo iezīmju skaitu, lai nodrošinātu sakarīgu informācijas attēlošanu gadījumos, ja ziņai pievienoti nesamērīgi daudz iezīmes
    */   
    
    'max_tags_count' => 5,
    
    /*
    |--------------------------------------------------------------------------
    | Ierakstu skaits dinamiskajā darbinieku meklēšanā
    |--------------------------------------------------------------------------
    |
    | Ar šo parametru var iestatīt, cik ierakstus attēlot dinamiskajā darbinieku meklēšanā. Jo lielāka vērība, jo lielāka slodze uz serveri
    */   
    
    'ajax_employees_count' => 100,
    
    /*
    |--------------------------------------------------------------------------
    | Izvēlnes grupas ID
    |--------------------------------------------------------------------------
    |
    | Arhitektrā, kad SVS un portāls ir 2 dažādos projektos, bet izmanto vienu datu bāzi, ir jānodala izvēlnes.
    | Ar šo parametru norāda, kādas izvēlnes rādīt SVS (tabulā dx_menu_group ir varianti)
    | 
    */   
    
    'menu_group_id' => 1,
    
    /*
    |--------------------------------------------------------------------------
    | Darbinieku tabulas nosaukums
    |--------------------------------------------------------------------------
    |
    | Darbinieki var būt atsevišķā tabulā, bet var arī izmantot to pašu tabulu, kas lietotājiem.
    | Konfigurācijas parametrā tāpēc var norādīt, kuru tabulu izmantot.
    | 
    */ 
    
    'empl_table' => 'dx_users',
    
    /*
    |--------------------------------------------------------------------------
    | Darbinieku tabulas lauki
    |--------------------------------------------------------------------------
    |
    | Norāda, kuri lauki atbilsts darbinieka nosaukumam un amatam
    | 
    */ 
    
    'empl_fields' => array(
            'empl_name' => 'display_name',
            'empl_position' => 'position_title',
            'empl_end_date' => 'valid_to'
        ),
    
    /*
    |--------------------------------------------------------------------------
    | Ignorējamo darbinieku ierakstu ID
    |--------------------------------------------------------------------------
    |
    | Ja darbinieku dati glabājas lietotāju tabulā, tad masīvā var nodādīt ierakstus, kuri nav jāattēlo darbinieku meklēšanas rezultātos
    | 
    */ 
    'empl_ignore_ids' => [1,2],
    
    /*
    |--------------------------------------------------------------------------
    | Publiskā portāla root foldera nosaukums (bez pilnā ceļa)
    |--------------------------------------------------------------------------
    |
    | Nepieciešams, lai izpildītu portāla komandas no CMS sistēmas
    | 
    */ 
    'portal_root_folder' => env('APP_PORTAL_ROOT_FOLDER', 'vk_public'),
    
    /*
    |--------------------------------------------------------------------------
    | Pazīme, vai pilnīgi visām lapām nepieciešama lietotāja autorizācija
    |--------------------------------------------------------------------------
    |
    | Ja uzstādīts šis parametrs, tad pēc noklusēšanas lietotājam vispirms būs jāautorizējas, lai piekļūtu jebkurai lapai neatkarīgi no iestatījuma public_user_id
    | 
    */   
    
    'is_all_login_required' => env('APP_LOGIN_REQUIRED', false),
    
    /*
    |--------------------------------------------------------------------------
    | Publisko resursu datņu foldera nosaukums. Tam jāatrodas uzreiz /public folderī
    |--------------------------------------------------------------------------
    |
    | Resursu datnes pievieno no satura redaktora
    | 
    */
    'resources_folder_name' => env('APP_RESOURCES_FOLDER', 'resources'),
    
     /*
    |--------------------------------------------------------------------------
    | Publisko resursu datņu kopējamo folderu ceļi
    |--------------------------------------------------------------------------
    |
    | Masīvs ar ceļiem, uz kurieni tiks kopēti resursu faili pēc to pievienošanas no satura redaktora.
    | Tas nepieciešams, ja ir cms un portāls kā atsevišķi projekti
    | Jānorāda pilnie ceļi, beigās nevajag likt slash, piemēram, jānorāda c:\cels\usz\folderi
    | 
    */
    'resources_copy_paths' => [        
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Darbinieku reģistra ID
    |--------------------------------------------------------------------------
    |
    | Norāda ID no tabulas dx_lists, kurā definēts reģistrs tabulai dx_users.
    | Parametru izmanto, lai darbinieku profilu blokam Block_EMPL_PROFILE var noteikt vai ir/nav profila rediģēšanas tiesības
    | 
    */
    
    'employee_list_id' => 259,
    
    /*
    |--------------------------------------------------------------------------
    | Employee profile page URL
    |--------------------------------------------------------------------------
    |
    | Here we need to provide relative path to employee profile route.
    | Blade view will add employee ID at the end of this route.
    | If not provided, then if user have rights on employee list - will open CMS form. If no rights - profile opening wont be possible.
    | Must start and end with slashes for example /employee/profile/
    | 
    */
    'employee_profile_page_url' => '',
    
    /*
    |--------------------------------------------------------------------------
    | UI setting for menu/slider
    |--------------------------------------------------------------------------
    |
    | It's possible to run Mindwo in 2 UIs:
    |   1) Menu on the left side - then set false
    |   2) No menu, but slidin panel - then set true
    */
    'is_slidable_menu' => false,
    
    /*
    |--------------------------------------------------------------------------
    | UI setting for menu
    |--------------------------------------------------------------------------
    |
    | It's possible to display menu in 2 ways:
    |   1) Menu on the left side - vertical - set false
    |   2) Meu on the top side - horizontal - set true
    */
    'is_horizontal_menu' => true,
    
    /*
    |--------------------------------------------------------------------------
    | UI setting for logo (not in login page but at the top left corner)
    |--------------------------------------------------------------------------
    |
    | Logo can be text or img. Textual setting is set in /resources/lang/en/index.php in parameter "logo_txt".
    | If it is set logo_txt then this setting "logo_small" will be ignored.
    */
    'logo_small' => env('APP_SMALL_LOGO','assets/global/logo/logo-default.png'),
    
    /*
    |--------------------------------------------------------------------------
    | UI setting for big logo used in login page
    |--------------------------------------------------------------------------
    */
    'logo_big' => 'assets/global/logo/mindwo_logo_big.png',
    
    /*
    |--------------------------------------------------------------------------
    | Working day hours - used for HR timeoff calculations
    |--------------------------------------------------------------------------
    */
    'working_day_h' => 8,
    
    /*
    |--------------------------------------------------------------------------
    | Default search - in top menu (must be set accroding to language)
    | English: Employees, Documents, News
    | Latvian: Darbinieki, Dokumenti, Ziņas
    |--------------------------------------------------------------------------
    */
    'default_search' => 'Documents',
    
    /*
    |--------------------------------------------------------------------------
    | Default time zone - see http://php.net/manual/en/timezones.europe.php
    |--------------------------------------------------------------------------
    */
    'time_zone' => 'Europe/Riga',
];
