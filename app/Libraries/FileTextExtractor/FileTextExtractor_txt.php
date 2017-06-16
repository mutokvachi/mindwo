<?php

namespace App\Libraries\FileTextExtractor
{    
    use Illuminate\Support\Facades\File;
    
    /**
     * Text extraction from txt file
     */
    class FileTextExtractor_txt extends FileTextExtractor
    {

        /**
         * Extracts file text
         * @return string
         */
        public function readText()
        {
            return File::get($this->filename);
        }

    }

}