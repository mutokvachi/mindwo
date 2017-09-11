<?php

namespace App\Libraries\Blocks
{

    use DB;
    use Request;
    use App\Exceptions;

    /**
     * Timeline widget
     */
    class Block_TIMELINE extends Block
    {

        /**
         * Izgūst bloka HTML
         * 
         * @return string Bloka HTML
         */
        public function getHTML()
        {
            return view('blocks.timeline')->render();
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
         * Izgūst bloka parametra vērtības un izpilda aktīvo ziņu izgūšanu masīvā
         * Parametrus norāda lapas HTML teksta veidā speciālos simbolos [[OBJ=...|SOURCE=...|ARTICLEPAGE=...|TAGSPAGE=...]]
         * 
         * @return void
         */
        protected function parseParams()
        {
            $this->addJSInclude(elixir('js/elix_timeline.js'));
        }
    }

}
