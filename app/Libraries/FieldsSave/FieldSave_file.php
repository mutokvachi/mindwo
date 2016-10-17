<?php

namespace App\Libraries\FieldsSave
{

    use \App\Exceptions;
    use Illuminate\Support\Facades\File;
    use Webpatser\Uuid\Uuid;
    use DB;
    use App\Libraries\FileTextExtractor;
    use App\Libraries\Facades\Image;
    use Config;
    use Log;

    /**
     * Faila lauka klase    
     * Objekts nodrošina faila (vai vairāku) vērtību apstrādi
     */
    class FieldSave_file extends FieldSave
    {

        /**
         * Datņu saglabāšanas kataloga ceļš, beidzas ar slīpsvītru
         * @var string
         */
        private $file_folder = "";

        /**
         * Apstrādā datnes lauka vērtību (izgūst datnes vai arī dzēš datnes)
         * Metode uzstāda datņu lauku vērtības masīvā
         */
        public function prepareVal()
        {
            if ($this->request->hasFile($this->fld->db_name)) {
                $this->processFiles();
                $this->is_val_set = 1;
            }
            else {
                $this->processRemovedFile();
            }
        }

        /**
         * Izgūst faila nosaukumu un guid vērtību kā masīvu
         * Ja augšupielādēti vairāki faili, tad atgriež masīvu ar nosaukums/guid kā ietverto masīvu
         */
        private function processFiles()
        {
            $this->file_folder = \App\Libraries\Helper::folderSlash(\App\Http\Controllers\FileController::getFileFolder($this->fld->is_public_file));

            $files = $this->request->file($this->fld->db_name);

            $this->is_multi_val = is_array($files); //(count($files) > 1) ? 1 : 0;

            if ($this->is_multi_val) {
                $this->uploadMultiFiles($files);
            }
            else {
                $this->uploadSingleFile($files, 0);
            }
        }

        /**
         *  Augšuplādē vairākas datnes
         * 
         * @param Array $files Masīvs ar datnēm
         */
        private function uploadMultiFiles($files)
        {
            foreach ($files as $key => $file) {
                $this->uploadSingleFile($file, $key);
            }
        }

        /**
         * Augšuplādē datni
         * 
         * @param UploadedFile $file Datnes objekts
         * @param integer $key Datnes kārtas indekss
         */
        private function uploadSingleFile($file, $key)
        {
            if ($file->isValid()) {
                $this->validateExtention($file);

                $this->uploadFile($file, $key);
            }
            else {
                $this->errorFile($file);
            }
        }

        /**
         * Apstrādā datnes dzēšanu vai arī pārbauda, ka datne nav mainīta un ir bijusi pievienota
         * Datne var tikt dzēsta, ja no HTML formas tiek padots parametrs, kas beidzas ar _removed
         */
        private function processRemovedFile()
        {
            $is_removed = $this->request->input(str_replace("_name", "_removed", $this->fld->db_name), 0);

            if ($is_removed == 1) {
                $this->setFileEmty();
            }
            else {
                $is_file_set = $this->request->input($this->fld->db_name . "_is_set", 0);

                if ($is_file_set == 1 && $this->item_id > 0) {
                    $this->is_val_set = 1;
                }
            }
        }

        /**
         * Datne tiek noņemta. Uzstāda attiecīgajiem laukiem null vērtības
         */
        private function setFileEmty()
        {
            if ($this->fld->is_required == 0) {
                $file_guid_name = str_replace("_name", "_guid", $this->fld->db_name); // Files are stored in 2 fields - one is real file name, other is GUID name (saved in server)
                $this->val_arr[$file_guid_name] = null;
                $this->val_arr[$this->fld->db_name] = null;

                if ($this->fld->is_text_extract) {
                    $file_extract_name = str_replace("_name", "_dx_text", $this->fld->db_name);
                    $this->val_arr[$file_extract_name] = null;
                }

                $this->is_val_set = 1;
            }
        }

        /**
         * Izgūst datni, saglabā to failsistēmā, uzstāda lauku vērtības masīvā
         * 
         * @param UploadedFile $file Datnes objekts
         * @param integer $key Datnes kārtas numurs
         */
        private function uploadFile($file, $key)
        {
            $file_name = $file->getClientOriginalName();

            //we store files on server with GUID names so they can be unique
            $target_file = Uuid::generate(4) . "." . File::extension($file_name);

            $file->move($this->file_folder, $target_file);

            // Izgūst folerus uz kuriem kopēt no db (primāri kopē uz db iestatījumos norādītiem. Ja tādu nav, skatīsies no konfiga)
            $paths = DB::table('dx_files_paths')
                    ->where('field_id', '=', $this->fld->field_id)
                    ->get();

            if (count($paths) == 0) {
                $paths = $this->getCopyFoldersArray();
            }
            $this->copyFiles($target_file, $paths);

            $file_guid_name = str_replace("_name", "_guid", $this->fld->db_name);

            $val = array();
            $val[$file_guid_name] = $target_file;
            $val[$this->fld->db_name] = $file_name;

            if ($this->fld->is_text_extract) {
                $file_text = FileTextExtractor\FileTextExtractorFactory::build_extractor($this->file_folder . $target_file)->readText();
                $file_extract_name = str_replace("_name", "_dx_text", $this->fld->db_name);
                $val[$file_extract_name] = $file_text;
            }

            if ($this->is_multi_val) {
                $this->val_arr[$key] = $val;
            }
            else {
                $this->val_arr = $val;
            }
        }

        /**
         * Ja datnes laukam piesaistīti kopējamie ceļi un formatēšana, tad kopē/formatē datni uz norādītajiem katalogiem
         * 
         * @param string $target_file Datnes nosaukums (unikāls GUIDs)
         * @param array $patsh Masīvs ar kopējamiem ceļiem
         * @throws Exceptions\DXCustomException
         */
        private function copyFiles($target_file, $paths)
        {

            foreach ($paths as $path) {

                if ($path->width > 0 && $path->height > 0) {
                    // formatējam attēlu (mainam izmērus)
                    if ($path->is_for_gallery) {
                        Image::resizeGalery($this->file_folder, $target_file, $path->width, $path->height, \App\Libraries\Helper::folderSlash($path->folder_path));
                    }
                    else {
                        Image::resize($this->file_folder, $target_file, $path->width, $path->height, \App\Libraries\Helper::folderSlash($path->folder_path));
                    }
                }
                else {
                    if (!File::copy($this->file_folder . $target_file, \App\Libraries\Helper::folderSlash($path->folder_path) . $target_file)) {
                        throw new Exceptions\DXCustomException("Sistēmas kļūda! Nav iepsējams kopēt datni '" . $this->file_folder . $target_file . "' uz katalogu '" . \App\Libraries\Helper::folderSlash($path->folder_path) . "'.");
                    }
                }
            }
        }

        /**
         * Iegūst masīvu no konfigurācijas datnes - masīva elementus pārveido kā objektus
         * CMS sistēma pieļauj, ka saglabājot datnes var tikt veikta to kopēšana balstoties uz informāciju db tabulā dx_files_paths vai arī konfugurācijas datni assets
         * 
         * @return array
         */
        private function getCopyFoldersArray()
        {
            $conf_path = 'assets.copy_paths.' . (($this->fld->is_public_file) ? 'public' : 'private') . "." . (($this->fld->is_image_file) ? 'images' : 'other');

            $conf_arr = Config::get($conf_path, array());

            $obj_arr = array();

            foreach ($conf_arr as $conf) {
                $object = new \stdClass();
                foreach ($conf as $key => $value) {
                    $object->$key = $value;
                }
                array_push($obj_arr, $object);
            }

            return $obj_arr;
        }

        /**
         * Apstrādā augšuplādējamās datnes kļūdu
         * 
         * @param UploadedFile $file Augšuplādējamās datnes objekts
         * @throws Exceptions\DXCustomException
         */
        private function errorFile($file)
        {
            $file_name = $file->getClientOriginalName();
            $err_msg = "";
            if ($file->getError() == 1) {
                // File upload size error
                $err_msg = "Nevar saglabāt datus! Datnei '" . $file_name . "' ir pārāk liels izmērs (" . $file->getClientSize() . " baiti)! Maksimālais pieļaujamais izmērs ir " . $file->getMaxFilesize() . " baiti.";
            }
            else {
                // Other error
                $err_msg = $file->getErrorMessage();
            }
            throw new Exceptions\DXCustomException($err_msg);
        }

        /**
         * Pārbauda, vai datnes paplašinājums ir atļauts (ir tabulā dx_files_headers)
         * 
         * @param UploadedFile $file Augšuplādējamās datnes objekts
         * @throws Exceptions\DXCustomException
         */
        private function validateExtention($file)
        {
            $file_name = $file->getClientOriginalName();
            $extention = File::extension($file_name);

            $header_row = DB::table('dx_files_headers')->where('extention', '=', $extention)->first();

            if (!$header_row) {
                throw new Exceptions\DXCustomException(sprintf(trans('errors.unsuported_file_extension'), $extention, $file_name));
            }
        }

    }

}