<?php

namespace App\Libraries\Blocks {

    use DB;
    use App\Exceptions;
    use Request;
    use Input;

    /**
     *
     * Biežāk uzdoto jautājumu klase      
     *
     */
    class Block_BONUS_CALC extends Block
    {
        /**
         * Izgūst bloka HTML
         * 
         * @return string Bloka HTML
         */
        public function getHTML()
        {
           return "demo";
        }

        /**
         * Izgūst bloka JavaScript
         * 
         * @return string Bloka JavaScript loģika
         */
        public function getJS()
        {
            return "";
        }

        /**
         * Izgūst bloka CSS
         * 
         * @return string Bloka CSS
         */
        public function getCSS()
        {
            //return view('pages.view_css_includes')->render();
            return "";
        }

        /**
         * Izgūst bloka JSON datus
         * 
         * @return string Bloka JSON dati
         */
        public function getJSONData()
        {
            return "";
        }

        /**
         * Izgūst bloka parametra vērtības
         * Parametrus norāda lapas HTML teksta veidā speciālos simbolos [[OBJ=...|SOURCE=...|TITLE=...]]
         * 
         * @return void
         */
        protected function parseParams()
        {
            //$this->addJSInclude(elixir('js/elix_view.js'));
        }

    }

}
