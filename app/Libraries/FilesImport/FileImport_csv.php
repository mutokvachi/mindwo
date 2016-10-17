<?php

namespace App\Libraries\FilesImport
{    
    /**
     * Data import from uploaded CSV file (.xlsx) into db
     */
    class FileImport_csv extends FileImport
    {
        /**
         * Prepare file for importing
         */
        public function processFile()
        {
            $this->csv_file = $this->file_name;
        }
    }

}