<?php

namespace mindwo\pages\Blocks
{

    use DB;

    /**
     * Svētku dienu klase.
     * Objekts nodrošina vārda dienu, svētku dienu un dzimšanas dienu kopskaita attēlošanu.
     */
    class Block_SPECDAYS extends Block
    {

        private $date = "";
        private $names = "";
        private $birth_count = 0;
        private $spec_day = "";
        private $dat_day_name = "";
        private $dat_day_nr = "";
        private $dat_month_name = "";

        /**
         * Izgūst bloka HTML
         * 
         * @return string Bloka HTML
         */
        public function getHTML()
        {
            return view('mindwo/pages::blocks.special_days', [
                        'date' => $this->date,
                        'names' => $this->names,
                        'spec_day' => $this->spec_day,
                        'birth_count' => $this->birth_count,
                        'dat_day_name' => $this->dat_day_name,
                        'dat_day_nr' => $this->dat_day_nr,
                        'dat_month_name' => $this->dat_month_name
                    ])->render();
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
         * Uzstāda bloka vērtības - vārda dienas, datumu, dzimšanas dienu skaitu
         * Šim blokam nav nekādu papildus parametru
         * 
         * @return void
         */
        protected function parseParams()
        {
            $name_row = DB::table('in_saints_days')
                    ->where('month', '=', date('n'))
                    ->where('day', '=', date('j'))
                    ->first();

            $this->names = $name_row->txt;
            $this->date = $this->getDateFormated();
            
            $this->spec_day = $name_row->spec_day;
        }

        /**
         * Atgriež šodienas datumu tekstuālā veidā.
         * Formāta piemērs: 1. Janvāris, Trešdiena
         * 
         * @return string Šodienas datums tekstuālā veidā
         */
        private function getDateFormated()
        {
            $week_days = trans('mindwo/pages::calendar.days_arr');
            $months = trans('mindwo/pages::calendar.month_arr');

            $m = date('n');
            $d = date('j');
            $w = date('w');

            $this->dat_day_name = $week_days[$w];
            $this->dat_day_nr = $d;
            $this->dat_month_name = $months[$m - 1];

            return $d . ". " . $months[$m - 1] . ", " . $week_days[$w];
        }

        /**
         * Izgūst šodienas dzimšanas dienu skaitu
         * 
         * @return integer Dzimšanas dienu skaits šodien
         */
        private function getBirthCount()
        {
            $sql = "
            SELECT
                    count(*) as cnt
            FROM
                    in_employees
            WHERE
                    month(birth_date) = month(now()) and day(birth_date) = day(now())
            ";

            $cnt_rows = DB::select($sql);

            $cnt = 0;
            if (count($cnt_rows) > 0) {
                $cnt = $cnt_rows[0]->cnt;
            }

            return $cnt;
        }

    }

}
