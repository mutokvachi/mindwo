<?php

namespace App\Libraries\FilesImport
{    
    use Illuminate\Support\Facades\File;
    use App\Exceptions;
    
    /**
     * Factory for data importing from file
     */
    class FileImportFactory
    {

        public static function build_file($file)
        {
            $ext = strtolower(File::extension($file->getClientOriginalName()));
            
            $class = "App\\Libraries\\FilesImport\\FileImport_" . $ext;

            if (!class_exists($class)) {
                throw new Exceptions\DXCustomException(sprintf(trans('errors.unsuported_file_extension'), $ext, $file->getClientOriginalName()));
            }

            return new $class($file);
        }

    }

}