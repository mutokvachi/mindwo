<?php
// LINUX - base_path('vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64')
// WINDOWS - base_path('vendor\wemersonjanuario\wkhtmltopdf-windows\bin\64bit\wkhtmltopdf.exe')

return array(


    'pdf' => array(
        'enabled' => true,
        'binary'  => base_path('vendor\wemersonjanuario\wkhtmltopdf-windows\bin\32bit\wkhtmltopdf.exe'),
        'timeout' => false,
        'options' => array(),
        'env'     => array(),
    ),
    'image' => array(
        'enabled' => false,
        'binary'  => '/usr/local/bin/wkhtmltoimage',
        'timeout' => false,
        'options' => array(),
        'env'     => array(),
    ),


);
