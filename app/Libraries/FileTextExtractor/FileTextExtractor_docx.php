<?php

namespace App\Libraries\FileTextExtractor
{
    use App\Exceptions;
    
    /**
     * Teksta izgūšana no Word DOCX formāta
     */
    class FileTextExtractor_docx extends FileTextExtractor
    {

        /**
         * Izgūst tekstu no datnes
         * @return string
         */
        public function readText()
        {
            $striped_content = '';
            $content = '';

            $zip = zip_open($this->filename);

            if (!$zip || is_numeric($zip)) {
                throw new Exceptions\DXCustomException("Nav iespējams izgūt tekstu no datnes! Nekorekta DOCX datne '" . $this->filename . "'.");
            }

            while ($zip_entry = zip_read($zip)) {

                if (zip_entry_open($zip, $zip_entry) == FALSE)
                    continue;

                if (zip_entry_name($zip_entry) != "word/document.xml")
                    continue;

                $content .= zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

                zip_entry_close($zip_entry);
            }

            zip_close($zip);

            $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $content);
            $content = str_replace('</w:r></w:p>', "\r\n", $content);
            $striped_content = strip_tags($content);

            return $striped_content;
        }

    }

}