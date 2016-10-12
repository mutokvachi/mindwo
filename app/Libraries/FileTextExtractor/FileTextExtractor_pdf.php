<?php

namespace App\Libraries\FileTextExtractor
{
    use App\Exceptions;
    
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
            $reader->setFilename($this->filename);
            $reader->decodePDF();

            $text = $reader->output();
            $text = str_replace("\r\n", "", $text); // windows
            $text = str_replace("\n", "", $text); // Linux
            $text = str_replace("\r", "", $text); // Linux, just in case
            
            return $text;
        }

    }

}