<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use File;
use App\Exceptions;
use App\Libraries\Facades\Image;

/**
 * Komanda formatē darbinieku attēlus uz samazinātu formātu - kopē uz folderi /formated_imd/small_avatar
 */
class FixImageSizes extends Command
{
    /**
     * Konsoles komandas nosaukums
     *
     * @var string
     */
    protected $signature = 'medus:fix_images';

    /**
     * Konsoles komandas apraksts
     *
     * @var string
     */
    protected $description = 'Konvertē darbinieku attēlus uz small_avatar folderi';
    
    /**
     * Folderis, kurā glabājas darbinieku oriģinālie attēli
     * 
     * @var string 
     */
    private $file_folder = "";
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->convertImages();
        
        $this->info('Images converted!');
    }
    
    /**
     * Darbinieku attēlu konvertēšana uz samazinātu izmēru
     */
    private function convertImages() {
        $paths = $this->getCopyFoldersArray();
        $users = DB::table('dx_users')->whereNotNull('picture_name')->get();
        $this->file_folder = $target_file = base_path() . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR;
        
        // Formatē avataru
        $target_file = get_portal_config('EMPLOYEE_AVATAR');
        $this->copyFiles($target_file, $paths);
        
        foreach($users as $user) {
        
            $target_file = $user->picture_guid;
            
            if (!File::exists($this->file_folder . $target_file)) {
                $this->info('Neatrod datni, izmantos avataru: ' . $this->file_folder . $target_file);
            }
            else {            
                $this->copyFiles($target_file, $paths);
            }
        }
    }
    
    /**
     * Atgriež masīvu ar kopēšanas ceļiem
     * @return array Masīvs ar kopēšanas ceļiem
     */
    private function getCopyFoldersArray() {
        $obj_arr = array();
        $object = new \stdClass();
        $object->width = 120;
        $object->height = 120;
        $object->folder_path = base_path() . DIRECTORY_SEPARATOR . "public" . DIRECTORY_SEPARATOR . 'formated_img' . DIRECTORY_SEPARATOR . 'small_avatar' . DIRECTORY_SEPARATOR;
        array_push($obj_arr, $object);
            
        return $obj_arr;
    }
    
    /**
     * Formatē un kopē norādīto datni
     * 
     * @param string $target_file Kopējamās datnes nosaukums
     * @param array $paths Masīvs ar kopēšanas ceļiem
     * @throws Exceptions\DXCustomException
     */
    private function copyFiles($target_file, $paths) {            
            
        foreach ($paths as $path) {                

            if ($path->width > 0 && $path->height > 0) {                    
                // formatējam attēlu (mainam izmērus)
                if (!File::exists($path->folder_path . $target_file)) {
                    Image::resize($this->file_folder, $target_file, $path->width, $path->height, $path->folder_path);
                }
                else {
                    $this->info('Jau ir formatēta datne: ' . $path->folder_path . $target_file);
                }
            }
            else {                    
                if (!File::copy($this->file_folder . $target_file, $path->folder_path . $target_file)) {
                    throw new Exceptions\DXCustomException("Sistēmas kļūda! Nav iepsējams kopēt datni '" . $this->file_folder . $target_file . "' uz katalogu '" . $path->folder_path . "'.");
                }
            }
        }
    }      
    
}