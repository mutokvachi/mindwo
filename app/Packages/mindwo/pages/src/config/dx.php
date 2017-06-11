<?php

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
    
    'feeds_page_rows_count' => 5,

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
    | Izmantojams formatēšanas funkcijās, lai pārvērstu datumu kā tekstu
    */ 
    'txt_date_format' => 'd.m.Y',
    
    
    /*
    |--------------------------------------------------------------------------
    | Funkciju datuma/laika formāts
    |--------------------------------------------------------------------------
    |
    | Izmantojams formatēšanas funkcijās, lai pārvērstu datumu un laiku kā tekstu
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
    | Arhitektrā, kad SVS un portāls ir 2 dažādos projektos, bet izmanto vienu datu bāzi, ir jānodala izvēlnes un lapas.
    | Ar šo parametru norāda, kādas izvēlnes un lapas rādīt SVS (tabulā dx_menu_group ir varianti)
    | 
    */   
    
    'menu_group_id' => 2,
    
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
    'portal_root_folder' => 'vk_public',
    
    /*
    |--------------------------------------------------------------------------
    | Pazīme, vai pilnīgi visām lapām nepieciešama lietotāja autorizācija
    |--------------------------------------------------------------------------
    |
    | Ja uzstādīts šis parametrs, tad pēc noklusēšanas lietotājam vispirms būs jāautorizējas, lai piekļūtu jebkurai lapai neatkarīgi no iestatījuma public_user_id
    | 
    */   
    
    'is_all_login_required' => false,
];