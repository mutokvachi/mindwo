<?php

namespace App\Libraries\Blocks
{

    use Config;
    use Input;
    use DB;

    /**
      Izdevumu kopskats
      Objekts nodrošina izdevumu kopskata attēlošanu
     */
    class Block_PUBLISH extends Block
    {

        private $skip = 0;
        private $publish = null;
        private $filt_month = 0;
        private $filt_year = 0;

        /**
         * Kopējais publikāciju skaits datu bāzē
         * 
         * @var integer 
         */
        private $total_count = 0;

        /**
         * Izgūst bloka HTML
         * 
         * @return string Bloka HTML
         */
        public function getHTML()
        {
            if ($this->skip == 0) {
                $criteria_all = "";

                if ($this->filt_year > 0) {
                    $criteria_all = addCriteria($criteria_all, $this->filt_year);
                }

                if ($this->filt_month > 0) {
                    $criteria_all = addCriteria($criteria_all, $this->filt_month);
                }

                return view('blocks.publish', [
                            'block_guid' => $this->block_guid,
                            'publish' => $this->publish,
                            'types' => DB::table('in_publish_types')->get(),
                            'years' => $this->getYears(),
                            'months' => $this->getMonths(),
                            'filt_month' => $this->filt_month,
                            'filt_year' => $this->filt_year,
                            'criteria_all' => $criteria_all,
                            'avatar' => get_portal_config('EMTY_PDF_AVATAR'),
                            'skip' => Config::get('dx.gallery_publish_item_count'),
                            'is_last' => (count($this->publish) == $this->total_count)
                        ])->render();
            }
            else {
                return view('blocks.publish_items', [
                            'publish' => $this->publish,
                            'avatar' => get_portal_config('EMTY_PDF_AVATAR')
                        ])->render();
            }
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
            return view('blocks.galeries_css')->render();
        }

        /**
         * Izgūst bloka JSON datus
         * 
         * @return string Bloka JSON dati
         */
        public function getJSONData()
        {
            return ['skip' => ($this->skip + Config::get('dx.gallery_publish_item_count')), 'is_last' => (($this->skip + count($this->publish)) == $this->total_count)];
        }

        /**
         * Izpilda galeriju izgūšanu masīvā
         *
         * @return void
         */
        protected function parseParams()
        {
            $this->publish = $this->getPublish();

            $this->addJSInclude('metronic/global/plugins/cubeportfolio/js/jquery.cubeportfolio.min.js');
            $this->addJSInclude('plugins/datetimepicker/jquery.datetimepicker.js');
            $this->addJSInclude('js/blocks/publish.js');

            // Lookup izkrītošās izvēlnes komponente
            $this->addJSInclude('plugins/select2/select2.min.js');
            $this->addJSInclude('plugins/select2/select2_locale_lv.js');
        }

        /**
         * Izgūst attēlojamo izdevumu masīvu
         * 
         * @return Array Izdevumu masīvs
         */
        private function getPublish()
        {
            $this->skip = Input::get('skip', 0); // šo prametru uzstāda tikai no AJAX pieprasījuma

            $this->filt_month = Input::get('month', '');

            $this->filt_year = Input::get('year', 0);

            $publish = DB::table('in_publish')
                    ->leftJoin('in_publish_types', 'in_publish_types.id', '=', 'in_publish.publish_type_id')
                    ->select(DB::raw("      
                                in_publish.*, 
                                in_publish_types.title as publish_type_title
                                "))
                    ->where(function($query)
            {
                if ($this->filt_month > 0) {
                    $query->whereRaw('month(in_publish.pub_date) = ' . $this->filt_month);
                }

                if ($this->filt_year > 0) {
                    $query->whereRaw('year(in_publish.pub_date) = ' . $this->filt_year);
                }
            });

            $this->total_count = $publish->count();
            
            $publish = $publish
                    ->orderBy('order_index', 'ASC')
                    ->orderBy('pub_date', 'DESC')
                    ->skip($this->skip)
                    ->take(Config::get('dx.gallery_publish_item_count'))
                    ->get();
           
            return $publish;
        }

        /**
         * Sagatavo tabulu ar izdevumu publicēšanas gadiem
         * 
         * @return Array Tabula ar gadiem
         */
        private function getYears()
        {
            return DB::table('in_publish')
                            ->select(DB::raw('year(pub_date) as y'))
                            ->groupBy(DB::raw('year(pub_date)'))
                            ->orderBy('y', 'DESC')
                            ->get();
        }

        /**
         * @return Array Masīvs ar mēnešiem.
         */
        private function getMonths()
        {
            return array(
                1 => 'Janvāris',
                2 => 'Februāris',
                3 => 'Marts',
                4 => 'Aprīlis',
                5 => 'Maijs',
                6 => 'Jūnijs',
                7 => 'Jūlijs',
                8 => 'Augusts',
                9 => 'Septembris',
                10 => 'Oktobris',
                11 => 'Novembris',
                12 => 'Decembris',
            );
        }

    }

}