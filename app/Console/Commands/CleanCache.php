<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Cleans cahce files in directory storage/app/cache except gitignore
 */
class CleanCache extends Command
{
    
    protected $signature = 'mindwo:clean_cache';
    
    protected $description = 'Cleans cache files in directory storage/app/cache';
        
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
        $arr_paths = [
            'config',
            'feed',
            'images',
            'menu',
            'scripts'
        ];        
        
        foreach($arr_paths as $dir) {
            $this->cleanDir($dir);
        }
        
        $this->info('Cache cleaned!');
    }
    
    private function cleanDir($dir) {
                
        $files = File::allFiles(storage_path() . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $dir);
        foreach ($files as $file)
        {
            if ($file != '.gitignore') {
                File::delete($file);
            }
        }
    }
         
    
}