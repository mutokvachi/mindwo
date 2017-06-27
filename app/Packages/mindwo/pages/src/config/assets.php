<?php

return array(
        'public_img_path' => '/img/', // Te jānorāda, kur glabāsies publiski pieejamie atteli, priekšā liekot /
        'private_file_path' => '/app/files/', // Te jānorāda, kur glabāsies nepubliski pievienotās dokumentu datnes, priekšā liekot /
    
	'images' => array(

        'paths' => array(
            'input' => 'public/img', // Te jānorāda CMS sistēmas katalogs, kurā tiek saglabātas datnes (ne caur satura redaktoru)
            'resources' => 'public/resources', // Te jānorāda CMS sistēmas katalogs, kurā tiek saglabātas datnes caur satura redaktoru
            'resources_route' => '/resources', // Te jānorāda resursu kataloga nosaukums, priekšā liekot /
            'output' => 'storage/app/cache/images', // Te jānorāda publiskā portāla katalogs, kurā tiks glabāti pielāgotie attēli
            'root_folder' => 'latvenergo_intranet' // Te jānorāda CMS sistēmas root kataloga nosaukums (bez pilnā ceļa, bez slash)
        ),

        'sizes' => array(
            'small' => array(
                'width' => 150,
                'height' => 100
            ),
            'big' => array(
                'width' => 600,
                'height' => 400
            ),
            'medium' => array(
                'width' => 180,
                'height' => 108
            ), 
            'small_avatar' => array(
                'width' => 29,
                'height' => 29
            ), 
            'employee_pic' => array(
                'width' => 76,
                'height' => 76
            ), 
            'employee_row' => array(
                'width' => 60,
                'height' => 60
            ), 
            'employee_100' => array(
                'width' => 100,
                'height' => 100
            ),
            'gallery_thumbn' => array(
                'width' => 305,
                'height' => 200
            ),
            'gallery_big' => array(
                'width' => 1220,
                'height' => 800
            ),
            'gallery_medium' => array(
                'width' => 180,
                'height' => 108
            )
        )
    )

);