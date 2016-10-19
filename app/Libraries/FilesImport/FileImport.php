<?php

namespace App\Libraries\FilesImport
{
    use Illuminate\Support\Facades\File;
    use \App\Exceptions;
    use Log;
    
    /**
     * Data import from uploaded file into db
     */
    abstract class FileImport
    {

        /**
         * Uploaded original file
         * @var object
         */
        public $file = null;

        /**
         * Temporary directory where uploaded file is stored
         * 
         * @var string 
         */
        public $tmp_dir = "";
        
        /**
         * Uploaded file name
         * 
         * @var string 
         */
        public $file_name = "";
        
        /**
         * CSV file name - Excels are converted to CSV
         * 
         * @var string
         */
        public $csv_file = "";

        /**
         * Process uploaded file
         */
        abstract protected function processFile();

        /**
         * File importing constructor
         *
         * @param  object $file File object
         * @return void
         */
        public function __construct($file)
        {
            $this->setTmpDir();
            
            $this->file = $file;
            
            $this->file_name = $this->file->getClientOriginalName();
            
            //save uploaded file in tmp dir
            $this->file->move($this->tmp_dir, $this->file_name);
        
            $this->processFile();
        }

        /**
        * Creates temporary folder where uploaded Excel file will be stored
        * 
        * @throws Exceptions\DXCustomException
        */
       private function setTmpDir()
       {
           $this->tmp_dir = base_path() . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . 'import_data_' . date('Y_n_d_H_i_s');

           try {
               if (!File::makeDirectory($this->tmp_dir)) {
                   throw new Exceptions\DXCustomException(sprintf(trans('errors.cant_create_folder'), $this->tmp_dir));
               }
           }
           catch (\Exception $e) {
               Log::info("Folder creation failed: " . $e->getMessage());
               throw new Exceptions\DXCustomException(sprintf(trans('errors.cant_create_folder'), $this->tmp_dir));
           }
       }

    }

}
