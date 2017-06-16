<?php

namespace mindwo\pages;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;
use Intervention\Image\ImageManager;

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
     * CMS sistēmas root katalogs
     * 
     * @var string 
     */
    private $file_origin_folder = "";

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

        $this->file_origin_folder = dirname(base_path()) . DIRECTORY_SEPARATOR . Config::get('assets.images.paths.root_folder');

        $this->file_not_found_path = storage_path() . DIRECTORY_SEPARATOR . "app" . DIRECTORY_SEPARATOR . "img_resize" . DIRECTORY_SEPARATOR . "not_found.png";
    }

    /**
     * Atgriež pieprasīto datni no CMS sistēmas kataloga
     * 
     * @param string $filename Datnes nosaukums
     * @return File Datnes objekts
     */
    public function getOriginal($filename)
    {
        $inputDir = $this->file_origin_folder . DIRECTORY_SEPARATOR . Config::get('assets.images.paths.input');
        $inputFile = $inputDir . DIRECTORY_SEPARATOR . $filename;

        if (File::isFile($inputFile)) {
            return File::get($inputFile);
        }
        else {
            return File::get($this->file_not_found_path);
        }
    }

    /**
     * Atgriež pieprasīto datni no CMS sistēmas resursu kataloga - tajā datnes pievieno no satura redaktora
     * 
     * @param string $filename Datnes nosaukums (pilns fiziskais ceļš)
     * @return File Datnes objekts
     */
    public function getResource($filename)
    {
        if (File::isFile($filename)) {
            return File::get($filename);
        }
        else {
            return File::get($this->file_not_found_path);
        }
    }

    /**
     * Izmaina attēla izmēru
     * 
     * @param string $filename      Attēla datnes nosaukums (datnes atrodas publiskajā folderī /img)
     * @param string $sizeString    Izmēra nosaukums. Visi izmēri konfigurējas konfigurācijas config/assets masīvā ar nosaukumu, platumu, augstumu
     * @return File Datne atbilstoši izmēram
     */
    public function resize($filename, $sizeString)
    {

        // We can read the output path from our configuration file.
        $outputDir = base_path() . DIRECTORY_SEPARATOR . Config::get('assets.images.paths.output');

        // Create an output file path from the size and the filename.
        $outputFile = $outputDir . DIRECTORY_SEPARATOR . $sizeString . '_' . $filename;

        // If the resized file already exists we will just return it.

        if (File::isFile($outputFile)) {
            return File::get($outputFile);
        }


        // File doesn't exist yet, so we will resize the original.
        $inputDir = $this->file_origin_folder . DIRECTORY_SEPARATOR . Config::get('assets.images.paths.input');
        $inputFile = $inputDir . DIRECTORY_SEPARATOR . $filename;

        if (!File::isFile($inputFile)) {
            $inputFile = $this->file_not_found_path;
        }

        // Get the width and the height of the chosen size from the Config file.
        $sizeArr = Config::get('assets.images.sizes.' . $sizeString);
        $width = $sizeArr['width'];
        $height = $sizeArr['height'];

        // We want to crop the image so we set the resize mode and size.
        $size = new \Imagine\Image\Box($width, $height);
        $mode = \Imagine\Image\ImageInterface::THUMBNAIL_INSET; //THUMBNAIL_OUTBOUND;
        // Create the output directory if it doesn't exist yet.
        if (!File::isDirectory($outputDir)) {
            File::makeDirectory($outputDir);
        }

        // Open the file, resize it and save it.
        $this->imagine->open($inputFile)
                ->thumbnail($size, $mode)
                ->save($outputFile, array('quality' => 90));

        // Return the resized file.
        return File::get($outputFile);
    }

    /**
     * Izmaina galerijas attēla izmēru
     * 
     * @param string $filename      Attēla datnes nosaukums (datnes atrodas publiskajā folderī /img)
     * @param string $sizeString    Izmēra nosaukums. Visi izmēri konfigurējas konfigurācijas config/assets masīvā ar nosaukumu, platumu, augstumu
     * @return File Datne atbilstoši izmēram
     */
    public function resizeGalery($filename, $sizeString)
    {

        // We can read the output path from our configuration file.
        $outputDir = base_path() . DIRECTORY_SEPARATOR . Config::get('assets.images.paths.output');

        // Create an output file path from the size and the filename.
        $outputFile = $outputDir . DIRECTORY_SEPARATOR . $sizeString . '_' . $filename;

        // If the resized file already exists we will just return it.

        if (File::isFile($outputFile)) {
            return File::get($outputFile);
        }


        // File doesn't exist yet, so we will resize the original.
        $inputDir = $this->file_origin_folder . DIRECTORY_SEPARATOR . Config::get('assets.images.paths.input');
        $inputFile = $inputDir . DIRECTORY_SEPARATOR . $filename;

        if (!File::isFile($inputFile)) {
            $inputFile = $this->file_not_found_path;
        }

        // Get the width and the height of the chosen size from the Config file.
        $sizeArr = Config::get('assets.images.sizes.' . $sizeString);
        $width = $sizeArr['width'];
        $height = $sizeArr['height'];

        $this->fitImage($inputFile, $outputFile, $width, $height);

        // Return the resized file.
        return File::get($outputFile);
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
        $inputDir = $this->file_origin_folder . DIRECTORY_SEPARATOR . Config::get('assets.images.paths.input');
        $inputFile = $inputDir . DIRECTORY_SEPARATOR . $filename;

        if (!File::isFile($inputFile)) {
            $inputFile = $this->file_not_found_path;
        }

        // Get the file mimetype using the Symfony File class.
        $file = new \Symfony\Component\HttpFoundation\File\File($inputFile);
        return $file->getMimeType();
    }

    /**
     * Izgūst resursa datnes satura tipa nosaukumu
     * 
     * @param string $filename Datne - pilnais fiziskais ceļš
     * @return string Datnes satura tipa nosaukums
     */
    public function getMimeTypeResource($filename)
    {
        if (!File::isFile($filename)) {
            $filename = $this->file_not_found_path;
        }

        // Get the file mimetype using the Symfony File class.
        $file = new \Symfony\Component\HttpFoundation\File\File($filename);
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
        $inputDir = $this->file_origin_folder . DIRECTORY_SEPARATOR . Config::get('assets.images.paths.input');
        $inputFile = $inputDir . DIRECTORY_SEPARATOR . $filename;

        if (!File::isFile($inputFile)) {
            $inputFile = $this->file_not_found_path;
        }

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
