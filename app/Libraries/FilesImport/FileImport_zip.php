<?php

namespace App\Libraries\FilesImport
{    
    use App\Exceptions;
    use Illuminate\Support\Facades\File;
    use Maatwebsite\Excel\Facades\Excel;
    
    /**
     * Data import from uploaded ZIP file (.zip) into db
     */
    class FileImport_zip extends FileImport
    {
        /**
         * Data file name from ZIP archive
         * 
         * @var string 
         */
        private $data_file = "";
        
        /**
         * Supported extensions for data file
         * 
         * @var array
         */
        private $suported_ext = ['xls', 'xlsx', 'csv'];
        
        /**
         * Prepare file for importing
         */
        public function processFile()
        {
            $this->exstractZIP();
            
            if (strlen($this->data_file) == 0) {
                throw new Exceptions\DXCustomException(sprintf(trans('errors.import_zip_no_data'), $this->file_name));
            }
            
            if (File::extension($this->data_file) != "csv") {
                // Convert to CSV        
                Excel::load($this->tmp_dir . DIRECTORY_SEPARATOR . $this->data_file, function($reader){
                })->store('csv', $this->tmp_dir)->save();

                $this->csv_file = pathinfo($this->data_file, PATHINFO_FILENAME) . ".csv";
            }
            else {
                $this->csv_file = $this->data_file;
            }
        }
        
        /**
         * Extracts all files from ZIP archive
         * 
         * @throws Exceptions\DXCustomException
         */
        private function exstractZIP() {
            $zip_file = $this->tmp_dir . DIRECTORY_SEPARATOR . $this->file_name;
            
            $zip = zip_open($zip_file);

            if (!$zip || is_numeric($zip)) {
                throw new Exceptions\DXCustomException(sprintf(trans('errors.import_zip_not_correct'), $this->file_name));
            }

            while ($zip_entry = zip_read($zip)) {

                $file = basename(zip_entry_name($zip_entry));
                $fp = fopen($this->tmp_dir . DIRECTORY_SEPARATOR . basename($file), "w+");

                if (zip_entry_open($zip, $zip_entry, "r")) {
                    $buf = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                    zip_entry_close($zip_entry);
                    
                    fwrite($fp, $buf);
                }
                
                fclose($fp);
                
                if (pathinfo($this->file_name, PATHINFO_FILENAME) == pathinfo($file, PATHINFO_FILENAME) && $this->isValidExtension($file)) {
                    
                    if (strlen($this->data_file) > 0) {
                        throw new Exceptions\DXCustomException(sprintf(trans('errors.import_zip_several_data'), $this->data_file, $file));
                    }
                    
                    $this->data_file = $file;
                }
            }

            zip_close($zip);
        }
        
        /**
         * Validates data file extension
         * 
         * @param string $file_name Data file name
         * @return boolean True - Extension is valid, False - invalid extension
         */
        private function isValidExtension($file_name) {
            $extention = File::extension($file_name);

            foreach ($this->suported_ext as $ext) {
                if ($ext == $extention) {
                    return true;
                }
            }
            
            return false;
        }
    }

}