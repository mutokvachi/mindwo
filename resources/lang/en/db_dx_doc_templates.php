<?php

/*
|--------------------------------------------------------------------------
|Labels for CMS register (grid and form labels) - table dx_doc_templates
|--------------------------------------------------------------------------
*/
return [
    
    'list_name' => 'Document templates',
    'item_name' => 'Document template',

    'list_id' => 'Register',
    'title' => 'Template name',
    'kind_id' => 'Template type',
    'description' => 'Description',
    'file_name' => 'Template file',
    'file_guid' => 'FIle GUID',
    'numerator_id' => 'Numerator',    
    'html_template' => 'Template text',
    'title_file' => 'Generated file name',

    'title_file_hint' => 'If not provided will be generated name with register ID and field ID. Here can be provided the same fields as in templates, for example, ${Registration number} - those fields will be replaced with actual values.',
    'html_template_hint' => 'Here can be inserted all fields available in views, for example, ${Registration number}, ${Full name} etc - those fields will be replaced with actual values.',
    'description_hint' => 'If register have only one template then here is no need to provide text. If several templates then it is good to provide here detailed description of the template - when it should be used.',

    // tabs
    'tab_main' => 'General',
    'tab_template' => 'Template text',
    'tab_filter' => 'Conditions',

    'js_info' => 'Show or hide file field and template content tab',

    'err_no_html' => 'Template text must be provided!',
    'err_no_file' => 'Template file must be provided!',
];