<?php

namespace mindwo\pages\Blocks {

    use DB;
    use mindwo\pages\Exceptions\PagesException;
    use Config;
    
    /**
     *
     * Calendar widget's class 
     *
     */
    class Block_CALENDAR extends Block
    {

        /**
         * Determines from which source load eneterprise events
         * @var integer
         */
        public $source_id = 0;

        /**
         * Block's title
         * @var string 
         */
        public $block_title = "Kalendārs";

        /**
         * Parameters if holidays are shown
         * @var int 
         */
        public $show_holidays = 1;

        /**
         * Parameters if birthdays are shown
         * @var int 
         */
        public $show_birthdays = 1;

        /**
         * Gets block's HTML
         * 
         * @return string Block's HTML
         */
        public function getHTML()
        {
            return view('blocks.calendar', [
                        'block_title' => $this->block_title,
                        'source_id' => $this->source_id,
                        'show_holidays' => $this->show_holidays,
                        'show_birthdays' => $this->show_birthdays,
                        'profile_url' => Config::get('dx.employee_profile_page_url', '')
                    ])->render();
        }

        /**
         * Gets block's JavaScript
         * 
         * @return string Block's JavaScript logic
         */
        public function getJS()
        {
            return '';
        }

        /**
         * Gets block's  CSS
         * 
         * @return string Block's CSS
         */
        public function getCSS()
        {
            return "";
        }

        /**
         * Gets block's JSON data
         * 
         * @return string Block's JSON data
         */
        public function getJSONData()
        {
            return "";
        }

        /**
         * Gets all widget parameters
         * Parameters are set in page HTML [[OBJ=...|SOURCE=...|TITLE=...]]
         * 
         * @return void
         */
        protected function parseParams()
        {
            $dat_arr = explode('|', $this->params);

            foreach ($dat_arr as $item) {
                $val_arr = explode('=', $item);

                if ($val_arr[0] == "SOURCE") {
                    $this->source_id = getBlockParamVal($val_arr);
                } elseif ($val_arr[0] == "TITLE") {
                    $this->block_title = str_replace("_", " ", getBlockParamVal($val_arr));
                } elseif ($val_arr[0] == "SHOW_HOLIDAYS") {
                    $this->show_holidays = getBlockParamVal($val_arr);
                } elseif ($val_arr[0] == "SHOW_BIRTHDAYS") {
                    $this->show_birthdays = getBlockParamVal($val_arr);
                } else if (strlen($val_arr[0]) > 0) {
                    throw new PagesException("Norādīts blokam neatbilstošs parametra nosaukums (" . $val_arr[0] . ")!");
                }
            }
        }
    }

}
