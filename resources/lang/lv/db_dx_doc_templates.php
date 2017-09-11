<?php

/*
|--------------------------------------------------------------------------
|Labels for CMS register (grid and form labels) - table dx_doc_templates
|--------------------------------------------------------------------------
*/
return [
    
    'list_name' => 'Dokumentu sagataves',
    'item_name' => 'Dokumenta sagatave',

    'list_id' => 'Reģistrs',
    'title' => 'Sagataves nosaukums',
    'kind_id' => 'Sagataves veids',
    'description' => 'Apraksts',
    'file_name' => 'Sagataves datne',
    'file_guid' => 'Datnes GUID',
    'numerator_id' => 'Numerators',    
    'html_template' => 'Sagataves teksts',
    'title_file' => 'Ģenerētās datnes nosaukums',

    'title_file_hint' => 'Ja nav norādīts, ģenerētajai datnei nosaukums tiks veidots no reģistra un ieraksta identifikatoriem. Var norādīt arī reģistra laukus formātā ${Lauka nosaukums} - ievietotie lauki tiks aizvietoti ar atbilstošajām vērtībām. Tādā veidā datnes nosaukumā var iekļaut, piemēram, reģistrācijas numuru un tml',
    'html_template_hint' => 'Sagataves tekstā var ievietot reģistra lauku nosaukumus formātā ${Lauka nosaukums} - ievietotie lauki tiks aizvietoti ar atbilstošajām vērtībā no reģistra.',
    'description_hint' => 'Ja reģistram ir paredzēta tikai viena sagatave, tad šo lauku nav nepieciešams norādīt. Ja vairākas sagataves, tad ir lietderīgi katrai sagatavei norādīt paskaidrojumu, kad sagatavi ieteicams izvēlēties.',

    // tabs
    'tab_main' => 'Pamatdati',
    'tab_template' => 'Sagataves teksts',
    'tab_filter' => 'Nosacījumi',

    'js_info' => 'Parāda vai paslēpj datnes lauku un sagataves teksta sadaļu atkarībā no izvēlētā sagataves veida.',

    'err_no_html' => 'Ir jānorāda sagataves teksts!',
    'err_no_file' => 'Ir jānorāda sagataves datne!',
];