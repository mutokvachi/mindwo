<?php

namespace App\Libraries\Image;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use \App\Exceptions;

/**
 * Attēlu apstrādāšanas klase (izmēru maiņa u.c.)
 */
class Image
{

    /**
     * Attēlu procesēšanas dzinis
     * 
     * @var Object 
     */
    protected $imagine;

    /**
     * Attēls, ko parādīt, ja pieprasītā datne nav atrasta
     * 
     * @var string 
     */
    private $file_not_found_path = "";

    /**
     * Attēlu apstrādāšanas klases konstruktors
     *      * 
     * @param Object $library   Attēlu procesēšanas dzinis
     */
    public function __construct($library = null)
    {
        if (!$this->imagine) {
            if (class_exists('Imagick')) {
                $this->imagine = new \Imagine\Imagick\Imagine();
            }
            else {
                $this->imagine = new \Imagine\Gd\Imagine();
            }
        }
        
        $this->file_not_found_path = dx_root_path() . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'img_resize' . DIRECTORY_SEPARATOR . 'img_not_found.jpg';
    }

    /**
     * Izmaina attēla izmēru
     * 
     * @param string $filename      Attēla datnes nosaukums (datnes atrodas publiskajā folderī /img)
     * @param string $sizeString    Izmēra nosaukums. Visi izmēri konfigurējas konfigurācijas config/assets masīvā ar nosaukumu, platumu, augstumu
     * @return File Datne atbilstoši izmēram
     */
    public function resize($source_folder, $filename, $width, $height, $dest_folder)
    {
        // Create an output file path from the size and the filename.
        $outputFile = $dest_folder . $filename;

        if (File::isFile($outputFile)) {
            throw new Exceptions\DXCustomException("Sistēmas kļūda! Nav iespējams formatēt datni '" . $filename . "' uz katalogu '" . $dest_folder . "', jo datne tur jau eksistē.");
        }
        
        $inputFile = $source_folder . $filename;

        if (!File::isFile($inputFile)) {
            throw new Exceptions\DXCustomException("Sistēmas kļūda! Nav iespējams formatēt datni '" . $filename . "' no kataloga '" . $source_folder . "', jo datne tur nav atrodama.");
        }

        // We want to crop the image so we set the resize mode and size.
        $size = new \Imagine\Image\Box($width, $height);
        $mode = \Imagine\Image\ImageInterface::THUMBNAIL_INSET; //THUMBNAIL_OUTBOUND;
        
        // Create the output directory if it doesn't exist yet.
        if (!File::isDirectory($dest_folder)) {
            if (!File::makeDirectory($dest_folder)) {
               throw new Exceptions\DXCustomException("Sistēmas kļūda! Nav iespējams izveidot katalogu '" . $dest_folder .  "'."); 
            }
        }

        // Open the file, resize it and save it.
        $this->imagine->open($inputFile)
                ->thumbnail($size, $mode)
                ->save($outputFile, array('quality' => 90));

        if (!File::isFile($outputFile)) {
            throw new Exceptions\DXCustomException("Sistēmas kļūda! Nav iespējams formatēt datni '" . $filename . "' uz katalogu '" . $dest_folder . "'.");
        }
    }

    /**
     * Izmaina galerijas attēla izmēru
     * 
     * @param string $filename      Attēla datnes nosaukums (datnes atrodas publiskajā folderī /img)
     * @param string $sizeString    Izmēra nosaukums. Visi izmēri konfigurējas konfigurācijas config/assets masīvā ar nosaukumu, platumu, augstumu
     * @return File Datne atbilstoši izmēram
     */
    public function resizeGalery($source_folder, $filename, $width, $height, $dest_folder)
    {

        // Create an output file path from the size and the filename.
        $outputFile = $dest_folder . $filename;

        if (File::isFile($outputFile)) {
            throw new Exceptions\DXCustomException("Sistēmas kļūda! Nav iespējams formatēt datni '" . $filename . "' uz katalogu '" . $dest_folder . "', jo datne tur jau eksistē.");
        }
        
        $inputFile = $source_folder . $filename;

        if (!File::isFile($inputFile)) {
            throw new Exceptions\DXCustomException("Sistēmas kļūda! Nav iespējams formatēt datni '" . $filename . "' no kataloga '" . $source_folder . "', jo datne tur nav atrodama.");
        }

        $this->fitImage($inputFile, $outputFile, $width, $height);

        if (!File::isFile($outputFile)) {
            throw new Exceptions\DXCustomException("Sistēmas kļūda! Nav iespējams formatēt galerijas datni '" . $filename . "' uz katalogu '" . $dest_folder . "'.");
        }
    }

    /**
     * Izgūst attēla datnes satura tipa nosaukumu
     * 
     * @param string $filename Attēla datne
     * @return string Datnes satura tipa nosaukumu
     */
    public function getMimeType($filename)
    {

        // Make the input file path.
        $inputDir = public_path() . DIRECTORY_SEPARATOR . Config::get('assets.images.paths.input');
        $inputFile = $inputDir . DIRECTORY_SEPARATOR . $filename;
        
        if (!File::isFile($inputFile)) {
            $inputFile = $this->file_not_found_path;
        }
        
        // Get the file mimetype using the Symfony File class.
        $file = new \Symfony\Component\HttpFoundation\File\File($inputFile);
        return $file->getMimeType();
    }

    /**
     * Uzstāda attēlam tekstu - iecentrēti
     * Attēlu izmērs 800x600 px. Teksta krāsa balta
     * 
     * @param string $filename  Attēla datnes nosaukums
     * @param string $text      Uz attēla novietojamais teksts
     * @return File Datne ar ievietoto tekstu
     */
    public function putText($filename, $text)
    {
        $inputDir = public_path() . DIRECTORY_SEPARATOR . 'img';
        $inputFile = $inputDir . DIRECTORY_SEPARATOR . $filename;

        $outputFile = $inputDir . DIRECTORY_SEPARATOR . "with_txt" . '_' . $filename;

        $jpg_image = imagecreatefromjpeg($inputFile);

        // Allocate A Color For The Text
        $white = imagecolorallocate($jpg_image, 255, 255, 255);

        // Set Path to Font File
        $font_path = public_path() . DIRECTORY_SEPARATOR . 'fonts' . DIRECTORY_SEPARATOR . 'Verdana.ttf';

        $font_size = 15;
        $angle = 0;
        $pos_x = 100;
        $pos_y = 200;
        $text_width = 600;

        $text = $this->wrap($font_size, $angle, $font_path, $text, $text_width);

        // Print Text On Image
        imagettftext($jpg_image, $font_size, $angle, $pos_x, $pos_y, $white, $font_path, $text);

        // Save Image to filesystem
        imagejpeg($jpg_image, $outputFile);

        // Clear Memory
        imagedestroy($jpg_image);

        return File::get($outputFile);
    }
    
     /**
     * Atgriež pieprasīto datni no CMS sistēmas kataloga
     * 
     * @param string $filename Datnes nosaukums
     * @return File Datnes objekts
     */
    public function getOriginal($filename) {
        $inputDir = public_path() . DIRECTORY_SEPARATOR . Config::get('assets.images.paths.input');;
        $inputFile = $inputDir . DIRECTORY_SEPARATOR . $filename;
        
        if (File::isFile($inputFile)) {
            return File::get($inputFile);
        }
        else {
            return File::get($this->file_not_found_path);
        }        
    }

    /**
     * Formatē attēlu atbilstoši norādītajam platumam un augstumam.
     * Vertikālo attēlu gadījumā tiek izveidotas melnas sānu joslas.
     * 
     * @param string $inputFile Sākotnējās attēla datnes pilnais ceļš
     * @param string $outputFile Reuzltējošās attēla datnes pilnais ceļš
     * @param integer $width Rezultējošais platums
     * @param integer $height Rezultējošais augstums
     */
    private function fitImage($inputFile, $outputFile, $width, $height)
    {
        $manager = new ImageManager;
        $img = $manager->make(file_get_contents($inputFile));

        if ($img->height() > $img->width()) {

            $this->scaleVerticalImage($manager, $img, $outputFile, $width, $height);
        }
        else {

            $this->scaleHorizontalImage($manager, $img, $inputFile, $outputFile, $width, $height);
        }
    }

    /**
     * Pielāgo izmēru vertikālā tipa attēlam (platums mazāks par augstumu).
     * Attēls tiek padarīts platāks, sānos ielikts melns fons.
     * 
     * @param object $manager Formatēšanas klases objekts
     * @param object $img Formatējamais attēla objekts
     * @param string $outputFile Reuzltējošās attēla datnes pilnais ceļš
     * @param integer $width Rezultējošais platums
     * @param integer $height Rezultējošais augstums
     */
    private function scaleVerticalImage($manager, $img, $outputFile, $width, $height)
    {
        $back_img_path = storage_path() . DIRECTORY_SEPARATOR . "app" . DIRECTORY_SEPARATOR . "img_resize" . DIRECTORY_SEPARATOR . "black_166_100.jpg";

        $img_black = $manager->make(file_get_contents($back_img_path));

        if ($img->height() > $height) {
            $img_black->resize($img->height(), $img->height());
        }
        else {
            $img_black->resize($width, $height);
        }

        $img_black->insert($img, 'center');

        $img_black->fit($width, $height);

        $img_black->save($outputFile);
    }

    /**
     * Pielāgo izmēru horizontālā tipa attēlam (platums lielāks vai vienāds par augstumu)
     * 
     * @param string $inputFile Sākotnējās attēla datnes pilnais ceļš
     * @param string $outputFile Reuzltējošās attēla datnes pilnais ceļš
     * @param integer $width Rezultējošais platums
     * @param integer $height Rezultējošais augstums
     */
    private function scaleHorizontalImage($manager, $img, $inputFile, $outputFile, $width, $height)
    {

        $size = new \Imagine\Image\Box($width, $height);
        $mode = \Imagine\Image\ImageInterface::THUMBNAIL_INSET; //THUMBNAIL_OUTBOUND;
        // Open the file, resize it and save it
        $this->imagine->open($inputFile)
                ->thumbnail($size, $mode)
                ->save($outputFile, array('quality' => 90));

        $back_img_path = storage_path() . DIRECTORY_SEPARATOR . "app" . DIRECTORY_SEPARATOR . "img_resize" . DIRECTORY_SEPARATOR . "white_166_100.jpg";

        $img_white = $manager->make(file_get_contents($back_img_path));
        $img_white->resize($width, $height);

        $img_fit = $manager->make(file_get_contents($outputFile));

        $img_white->insert($img_fit, 'center');

        $img_white->save($outputFile);
    }

    /**
     * Ievieto tekstā jaunas līnijas simbolu \n, ja teksts nesaiet norādītajā platumā
     * 
     * @param integer $fontSize Teksta fonta izmērs
     * @param integer $angle Teksta pagriešanas lenķis ( 0 - normāli, 90 - attiecīgajos grādos)
     * @param string $fontFace Ceļš uz fonta failu
     * @param string $string Apstrādājamais teksts
     * @param integer $width Teksta bloka platums
     * @return string Apstrādāts teksts ar ievietotajām līnijām
     */
    private function wrap($fontSize, $angle, $fontFace, $string, $width)
    {

        $ret = "";

        $arr = explode(' ', $string);

        foreach ($arr as $word) {

            $teststring = $ret . ' ' . $word;
            $testbox = imagettfbbox($fontSize, $angle, $fontFace, $teststring);
            if ($testbox[2] > $width) {
                $ret.=($ret == "" ? "" : "\n") . $word;
            }
            else {
                $ret.=($ret == "" ? "" : ' ') . $word;
            }
        }

        return $ret;
    }

}