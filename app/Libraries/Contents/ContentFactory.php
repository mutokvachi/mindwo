<?php

namespace App\Libraries\Contents
{

    use App\Exceptions;
    use DB;
    
    class ContentFactory
    {
        /**
          *
          * Portāla satura izveidošanas klase
          *
          *
          * Objekts izveido portāla satura (raksts, video, attēli) klasi
          *
         */

        /**
         * Izveido portāla satura objektu
         * 
         * @param  string $config_name   Konfigurācijas iestatījuma nosaukums 
         * @return Object                Konfigurācijas lauka objekts
         */

        public static function build_content($item_row)
        {
            $type_row = DB::table('in_article_types')->where('id', '=', $item_row->type_id)->first();

            try
            {
                $type = $type_row->code;
            }
            catch (\Exception $e)
            {
                throw new Exceptions\DXCustomException("Nav identificējams portāla satura tips!");
            }

            $class = "App\\Libraries\\Contents\\Content_" . $type;

            if (class_exists($class))
            {
                return new $class($item_row);
            }
            else
            {
                throw new Exceptions\DXCustomException("Neatbalstīts portāla satura tips '" . $type . "'!");
            }
        }

    }

}