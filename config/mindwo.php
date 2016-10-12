<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | Masīvs ar kļūdu tipiem un to apstrādes klasēm
    |--------------------------------------------------------------------------
    |
    | Šajā masīvā var definēt apstrādājamos kļūdu tipus un norādīt tiem atbilstošās apstrādes klases
    */
    
    'exceptions' => [
        ['class' => '\Symfony\Component\HttpKernel\Exception\NotFoundHttpException', 'handler' => 'ExceptionPageNotFound'],
        ['class' => 'TokenMismatchException', 'handler' => 'ExceptionSessionEnded'],
        ['class' => '\App\Exceptions\DXExtendedException', 'handler' => 'ExceptionCustom'],
        ['class' => '\App\Exceptions\DXCustomException', 'handler' => 'ExceptionCustom'],
        ['class' => '\mindwo\pages\Exceptions\PagesException', 'handler' => 'ExceptionCustom'],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Masīvs ar palīgfunkciju datņu nosaukumiem
    |--------------------------------------------------------------------------
    |
    | Šajā masīvā var definēt palīgfunkciju datņu nosaukumus.
    | Palīgfunkcijas tiek ielādētas globāli pieejamas ar HelpersServiceProviders.
    | Palīgfunkciju datnes atrodas pakotnes folderī Helpers
    */
    
    'helpers' => [
        "blocks.php",
        "pages.php",
        "dates.php",
        "views_defaults.php",
        "texts.php",
        "DxHelper.php",
        "forms.php"
    ],
    
    
    /*
    |--------------------------------------------------------------------------
    | Ierakstu skaits galeriju un izdevumu lapas skatījumā
    |--------------------------------------------------------------------------
    */   
    
    'gallery_publish_item_count' => 9,
    
   
];