<?php

namespace App\Libraries\FilesImport
{    
    use Maatwebsite\Excel\Facades\Excel;
    use Log;
    
    /**
     * Data import from uploaded Excel file (.xls) into db
     */
    class FileImport_xls extends FileImport
    {
        
        /**
         * Prepare file for importing
         */
        public function processFile()
        {
            // Convert to CSV        
            Excel::load($this->tmp_dir . DIRECTORY_SEPARATOR . $this->file_name, function($reader){                
            })->store('csv', $this->tmp_dir)->save();

            $this->csv_file = pathinfo($this->file_name, PATHINFO_FILENAME) . ".csv";
        }

    }

}