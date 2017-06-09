<?php 

namespace mindwo\pages\Facades;

use Illuminate\Support\Facades\Facade;

class Image extends Facade {

    protected static function getFacadeAccessor()
    {
        return new \mindwo\pages\Image;
    }

}