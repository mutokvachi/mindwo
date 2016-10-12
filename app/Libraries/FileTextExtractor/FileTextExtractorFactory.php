<?php

namespace App\Libraries\FileTextExtractor
{

    use App\Exceptions;

    /**
     * Datnes teksta izgūšanas izveidošanas klase
     * Objekts izveido datnes teksta izgūšanas objektu atkarībā no datnes tipa
     */
    class FileTextExtractorFactory
    {

        /**
         * Izveido datnes teksta izgūšanas objektu atkarībā no datnes tipa
         * 
         * @param string $filePath Pilnais ceļš uz datni
         * @return \App\Libraries\FileTextExtractor\class
         * @throws Exceptions\DXCustomException
         */
        public static function build_extractor($filePath)
        {
            $fileArray = pathinfo($filePath);
            $file_ext = $fileArray['extension'];

            $class = "App\\Libraries\\FileTextExtractor\\FileTextExtractor_" . $file_ext;

            if (class_exists($class)) {
                return new $class($filePath);
            }
            else {
                $class = "App\\Libraries\\FileTextExtractor\\FileTextExtractor_none";
                return new $class($filePath);
            }
        }

    }

}