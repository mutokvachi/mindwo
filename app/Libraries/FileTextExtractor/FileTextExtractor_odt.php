<?php

namespace App\Libraries\FileTextExtractor
{
    use App\Exceptions;
    use ZipArchive;
    
    /**
     * Teksta izgūšana no OpenOffice ODT formāta
     */
    class FileTextExtractor_odt extends FileTextExtractor
    {

        /**
         * Izgūst tekstu no datnes
         * @return string
         */
        public function readText()
        {
            $xml_filename = "content.xml"; //content file name
            $zip_handle = new ZipArchive;
            $output_text = "";
            
            if(true === $zip_handle->open($this->filename)){
                if(($xml_index = $zip_handle->locateName($xml_filename)) !== false){
                        $xml_datas = $zip_handle->getFromIndex($xml_index);
                        
                        $dom = new \DOMDocument();
                        $dom->loadXML($xml_datas, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
                        
                        $output_text = html_entity_decode(strip_tags($dom->saveXML()));
                }else{
                        throw new Exceptions\DXCustomException("Nav iespējams izgūt tekstu no datnes! Nekorekta ODT datne '" . $this->filename . "'.");
                }
                $zip_handle->close();
            }else{
                throw new Exceptions\DXCustomException("Nav iespējams izgūt tekstu no datnes! Nekorekta ODT datne '" . $this->filename . "'.");
            }
            
            return $output_text;
        
        }

    }

}