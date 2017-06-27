<?php

namespace App\Libraries\FileTextExtractor
{    
    /**
     * Teksta izgūšana no PDF datnes
     */
    class FileTextExtractor_pdf extends FileTextExtractor
    {

        /**
         * Izgūst tekstu no datnes
         * @return string
         */
        public function readText()
        {
            $reader = new \Asika\Pdf2text;
            
            $text = $reader->decode($this->filename);
            
            $text = str_replace("\r\n", "", $text); // windows
            $text = str_replace("\n", "", $text); // Linux
            $text = str_replace("\r", "", $text); // Linux, just in case
            
            return $text;
        }

    }

}