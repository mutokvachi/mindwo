<?php

namespace App\Libraries\FileTextExtractor
{
    use ZipArchive;
    
    /**
     * Teksta izgūšana no PowerPoint pptx formāta
     */
    class FileTextExtractor_pptx extends FileTextExtractor
    {

        /**
         * Izgūst tekstu no datnes
         * @return string
         */
        public function readText()
        {
            $zip_handle = new ZipArchive;
            $output_text = "";
            
            if (true === $zip_handle->open($this->filename)) {
                $slide_number = 1; //loop through slide files
                while (($xml_index = $zip_handle->locateName("ppt/slides/slide" . $slide_number . ".xml")) !== false) {
                    $xml_datas = $zip_handle->getFromIndex($xml_index);
                    
                    $dom = new \DOMDocument();
                    $dom->loadXML($xml_datas, LIBXML_NOENT | LIBXML_XINCLUDE | LIBXML_NOERROR | LIBXML_NOWARNING);
                        
                    $output_text .= html_entity_decode(strip_tags($dom->saveXML()));
                    $slide_number++;
                }
                if ($slide_number == 1) {
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