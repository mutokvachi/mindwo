<?php

namespace App\Libraries\Structure
{
    use Illuminate\Support\Facades\File;
    use Input;
    use DB;
    use App\Exceptions;
    use Log;
    
    /**
     *
     * Nevajadzīgo datņu dzēšanas klase
     *
     *
     * Objekts nodrošina nevajadzīgo datņu dzēšanu (datnes tiek dzēstas no kataloga /img
     *
     */
    class StructMethod_delete_unneded_files extends StructMethod
    {
        
        private $is_backup = 0;
        
        private $copy_dir = "";
        
        /**
         * Inicializē klases parametrus
         * 
         * @return void
         */
        public function initData()
        {      
            $this->is_backup = Input::get('is_backup', 0);
            
            if ($this->is_backup) {
                $this->copy_dir = base_path() . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . "img_backup_" . date('Y_n_d_H_i_s');
                
                try {
                    if (!File::makeDirectory($this->copy_dir)) {
                        throw new Exceptions\DXCustomException(sprintf(trans('errors.cant_create_folder'), $this->copy_dir));
                    }
                }
                catch(\Exception $e) {
                    Log::info("Kataloga veidošanas kļūda: " . $e->getMessage());
                    throw new Exceptions\DXCustomException(sprintf(trans('errors.cant_create_folder'), $this->copy_dir));                    
                }
            }
        }

        /**
         * Atgriež reģistra dzēšanas uzstādījumu HTML formu
         * 
         * @return string HTML forma
         */
        public function getFormHTML()
        {
            return view('structure.delete_unneded_files', [
                        'form_guid' => $this->form_guid
                    ])->render();
        }

        /**
         * Dzēš skatu un tā laukus
         * 
         * @return void
         */
        public function doMethod()
        {
            $path = base_path() . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'img';
            
            $files = File::allFiles($path);
            foreach ($files as $file)
            {
                if (!$this->isFileNeeded($file)) {
                    $this->deleteFile($file);
                }
            }
        }
        
        /**
         * Dzēš vai kopē norādīto datni
         * @param Object $file Datnes objekts
         * @throws Exceptions\DXCustomException
         */
        private function deleteFile($file) {
            if ($this->is_backup) {
                if ( !File::move($file, $this->copy_dir . DIRECTORY_SEPARATOR . basename($file)))
                {
                    throw new Exceptions\DXCustomException("Nav iespējams nokopēt datni " . $file . " uz katalogu " . $this->copy_dir . "!");
                }
            }
            else {
                File::delete($file);
            }              
        }
        
        /**
         * Pārbauda, vai datne tiek izmantota kādā no tabulām
         * 
         * @param Object $file Datnes objekts
         * @return boolean True, ja tiek izmantota, False, ja netiek
         */
        private function isFileNeeded($file) {
            $flds = DB::table('dx_lists_fields as lf')
                    ->join('dx_lists as l', 'lf.list_id', '=', 'l.id')
                    ->join('dx_objects as o', 'l.object_id', '=', 'o.id')
                    ->select(DB::raw('lf.db_name as fld_name, o.db_name as tbl_name'))
                    ->where('lf.type_id', '=', 12)
                    ->get();
            
            foreach($flds as $fld) {
                $guid_name = str_replace("_name", "_guid", $fld->fld_name);
                $cnt = DB::table($fld->tbl_name)->where($guid_name, '=', basename($file))->count();
                
                if ($cnt > 0) {
                    return true;
                }
            }
            
            return false;
        }

    }

}