<?php

namespace App\Libraries\FileTextExtractor
{
    
    /**
     * Neatbalstītas datnes tipa apstrāde - nevar izgūt tekstu, atgreiež null
     */
    class FileTextExtractor_none extends FileTextExtractor
    {

        /**
         * Nevar izgūt tekstu no datnes - atgriež nulll
         * @return string
         */
        public function readText()
        {
            return null;        
        }

    }

}