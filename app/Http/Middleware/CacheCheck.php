<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\File;
use Config;
use Log;

/**
 * Cache failu aktualitātes pārbaudes klase
 * 
 * Mainoties datu bāzei ir jādzēš vecie cache faili - šī klase pārbauda, vai nav mainīta db.
 * Ja db, mainīta, tad tiek dzēsti cache faili (kuros parasti ielādē informāciju no db, lai palielinātu ātrdarbību)
 */
class CacheCheck
{    

    /**
     * Pārbauda cache aktualitāti
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle($request, Closure $next)
    {
        $cache_path = dx_root_path() . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'current_db.txt';
        
        $cache_db = "";
        if (File::exists($cache_path)) {
            $cache_db = File::get($cache_path);
        }

        $current_db = Config::get('database.connections.mysql.database');
        
        if ($cache_db != $current_db) {
            
            $this->emptyFolder('config');
            $this->emptyFolder('feed');
            $this->emptyFolder('menu');
            $this->emptyFolder('images');
            
            $bytes_written = File::put($cache_path, $current_db);
            if ($bytes_written === false)
            {
                Log::info('Nav iespējams uzstādīt aktuālās datu bāzes konfigurācijas parametru! Iespējams, nav tiesību rakstīt folderī storage/app/cache/folder');
            }
            else {
                
            }
            Log::info('Tika mainīta datu bāze no ' . $cache_db . ' uz ' . $current_db . '! Tādēļ tika dzēstas arī saistītās cache datnes.');
        }
        
        return $next($request);
    }
    
    /**
     * Dzēš visas datnes no norādītā kataloga. Izņemot nedzēš .gitignore
     * @param string $folder Kataloga nosaukums (bez pilnā ceļa, jāatrodas storage\app\cache katalogā
     */
    private function emptyFolder($folder) {
        $path = dx_root_path() . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . $folder;
        
        $files = File::allFiles($path);
        foreach ($files as $file)
        {
            if ($file != ".gitignore") {
                File::delete($file);
            }
        }
    }

}
