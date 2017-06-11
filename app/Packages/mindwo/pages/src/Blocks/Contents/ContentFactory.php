<?php

namespace mindwo\pages\Blocks\Contents
{

    use mindwo\pages\Exceptions\PagesException;
    use DB;

    /**
     * Portāla satura izveidošanas klase.
     * Objekts izveido portāla satura (raksts, video, attēli) klasi.
     */
    class ContentFactory
    {

        /**
         * Izveido portāla satura objektu
         * 
         * @param  string $config_name   Konfigurācijas iestatījuma nosaukums 
         * @return Object                Konfigurācijas lauka objekts
         */
        public static function build_content($item_row)
        {
            $type_row = DB::table('in_article_types')->where('id', '=', $item_row->type_id)->first();

            try {
                $type = $type_row->code;
            }
            catch (\Exception $e) {
                throw new PagesException("Nav identificējams portāla satura tips!");
            }

            $class = "mindwo\\pages\\Blocks\\Contents\\Content_" . $type;

            if (class_exists($class)) {
                return new $class($item_row);
            }
            else {
                throw new PagesException("Neatbalstīts portāla satura tips '" . $type . "'!");
            }
        }

    }

}