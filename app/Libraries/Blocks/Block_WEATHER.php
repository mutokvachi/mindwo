<?php

namespace App\Libraries\Blocks
{

    /**
     *
     * Laika ziņu klase
     *
     *
     * Objekts nodrošina laika ziņu informācijas attēlošanu.
     * Ja laika ziņas nav sasinhronizētas ar METEO, tad atgriež tukšumu.
     *
     */
    class Block_WEATHER extends Block
    {

        /**
         * Izgūst bloka HTML
         * 
         * @return string Bloka HTML
         */
        public function getHTML()
        {
            $wdata = $this->fetchMeteoData();
            return view('blocks.weather', ['wdata' => $wdata])->render();
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
            return view('blocks.weather_css')->render();
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
         * Šim blokam metode nav nepieciešama
         * 
         * @return void
         */
        protected function parseParams()
        {            
        }

        /**
         * Iegūst datus no meteo portāla.
         * 
         * @return mixed
         */
        private function fetchMeteoData()
        {
            $ch = curl_init('http://meteo.energo.lv/index.php?action=jsonMarkers');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Requested-With: XMLHttpRequest", "Content-Type: application/json; charset=utf-8"));
            $output = curl_exec($ch);
            curl_close($ch);

            return $output;
        }

    }

}