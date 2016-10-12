<?php

namespace App\Libraries\FileTextExtractor
{

    /**
     *
     * Datņu teksta izgūšanas abstraktā klase
     *
     */
    abstract class FileTextExtractor
    {
        /**
         * Datnes pilnais ceļš, no kuras tiks izvilkts teksts
         * @var string 
         */
        public $filename = null;
        
        /**
         * Abstraktā funkcija teksta izgūšanai - katram datnes tipam sava loģika
         */
        abstract function readText();

        /**
         * Konstruktors teksta izgūšanas klasei
         * 
         * @param string $filePath Pilnais ceļš uz datni, no kuras jāizgūst teksts
         */
        public function __construct($filePath)
        {
            $this->filename = $filePath;
        }

    }

}
