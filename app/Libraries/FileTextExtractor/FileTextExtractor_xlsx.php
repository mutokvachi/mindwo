<?php

namespace App\Libraries\FileTextExtractor
{
    use ZipArchive;
    
    /**
     * Teksta izgūšana no Excel xlsx formāta
     */
    class FileTextExtractor_xlsx extends FileTextExtractor
    {

        /**
         * Izgūst tekstu no datnes
         * @return string
         */
        public function readText()
        {
            $xml_filename = "xl/sharedStrings.xml"; //content file name
            $zip_handle = new ZipArchive;
            $output_text = "";
            
            if (true === $zip_handle->open($this->filename)) {
                if (($xml_index = $zip_handle->locateName($xml_filename)) !== false) {
                    $xml_datas = $zip_handle->getFromIndex($xml_index);
                    
                    $dom = new \DOMDocument();
                    $dom->loadXML($xml_datas, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
                        
                    $output_text = html_entity_decode(strip_tags($dom->saveXML()));
                }
                else {
                    $output_text .="";
                }
                $zip_handle->close();
            }
            else {
                $output_text .="";
            }
            
            return $output_text;
        }

    }

}