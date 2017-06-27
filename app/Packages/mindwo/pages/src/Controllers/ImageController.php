<?php

namespace mindwo\pages\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use mindwo\pages\Facades\Image;
use Route;
use Config;

/**
 *
 * Attēlu apstrādes kontrolieris    *
 *
 * Kontrolieris nodrošina automātisku attēlu izmēru izmainīšanu atbilstoši norādītajai konfigurācijai
 * Attēlu izmērus un konfigurācijas nosaukumus var norādīt config/assets
 * Ģenerētie izmainītie attēli tiek saglabāti storage/app/cache/images
 * Jābūt ieslēgtiek sekojošiem PHP.ini parametriem:
 *   extension=php_fileinfo.dll
 *   extension=php_gd2.dll vai extension=php_imagick.dll
 *
 */
class ImageController extends Controller
{

    /**
     * Izgūst norādītajam izmēram atbilstošu attēlu
     * Pirmo reizi attēls tiek automātiski izmainīts. Nākamās reizes attēlu izgūst no cache.
     *
     * @param   string     $size     Izmēra nosaukums
     * @param   mixed      $filename Datnes nosaukums (no publiskā foldera img)
     * @return  Response   Attēla datne atbilstošā izmērā
     */
    public function getImage($size, $filename)
    {
        return (new Response(Image::resize($filename, $size), 200))
                        ->header('Content-Type', Image::getMimeType($filename));
    }
    
    /**
     * Atgriež pieprasīto datni no CMS sistēmas kataloga "img"
     * 
     * @param string $filename Datnes nosaukums
     */
    public function getOriginalFile($filename)
    {
        return (new Response(Image::getOriginal($filename), 200))
                        ->header('Content-Type', Image::getMimeType($filename));
    }
    
    /**
     * Atgriež pieprasīto datni no CMS sistēmas kataloga "resources" - te glabājas no satura redaktora pievienotās datnes
     * 
     * @param string $filename Datnes (ar katalogu) nosaukums
     */
    public function getResourcesFile($filename)
    {
        $route = str_replace(Config::get('assets.images.paths.resources_route'), "", Route::currentRouteName());
        
        $filename = $route . DIRECTORY_SEPARATOR . $filename;
        
        $path = dirname(base_path()) . DIRECTORY_SEPARATOR . Config::get('assets.images.paths.root_folder') . DIRECTORY_SEPARATOR . Config::get('assets.images.paths.resources') . $filename;
        
        return (new Response(Image::getResource($path), 200))
                        ->header('Content-Type', Image::getMimeTypeResource($path));
    }

    /**
     * Izgūst norādītajam izmēram atbilstošu attēlu priekš galerijām
     * Pirmo reizi attēls tiek automātiski izmainīts. Nākamās reizes attēlu izgūst no cache.
     *
     * @param   string     $size     Izmēra nosaukums
     * @param   mixed      $filename Datnes nosaukums (no publiskā foldera img)
     * @return  Response   Attēla datne atbilstošā izmērā
     */
    public function getImageGalery($size, $filename)
    {
        return (new Response(Image::resizeGalery($filename, $size), 200))
                        ->header('Content-Type', Image::getMimeType($filename));
    }

    /**
     * Ievieto uz attēla norādīto tekstu
     * 
     * @param string $filename  Attēla datnes nosaukums
     * @param string $text      Uz attēla ievietojamais teksts
     * @return Response Attēla datne ar ievietoto tekstu
     */
    public function getImageText($filename, $text)
    {
        return (new Response(Image::putText($filename, $text), 200))
                        ->header('Content-Type', Image::getMimeType($filename));
    }

}
