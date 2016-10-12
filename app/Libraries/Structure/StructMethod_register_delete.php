<?php

namespace App\Libraries\Structure
{

    use Input;
    use DB;
    use App\Exceptions;
    use Log;
    
    /**
     *
     * Reģistra dzēšanas klase
     *
     * Objekts nodrošina reģistra dzēšanu. Datu bāzē definēts CASCADE DELETE saistītajiem datiem - tādēļ tiek automātiski dzēsti saistītie ieraksti.
     * Dzēšot no saskarnes tiek kontrolētas relācijas loģiskā līmenī un pa tiešo izdzēst reģistru nemaz nevar, jo vispirms jādzēš saistītie ieraksti (formas, skati utt)
     *
     */
    class StructMethod_register_delete extends StructMethod
    {

        private $obj_id = 0;
        private $list_id = 0;

        /**
         * Inicializē klases parametrus
         * 
         * @return void
         */
        public function initData()
        {
            $this->obj_id = Input::get('obj_id', 0);

            if ($this->obj_id == 0) {
                $this->obj_id = Input::get('item_id', 0);
            }

            $this->list_id = Input::get('list_id', 0);
        }

        /**
         * Atgriež reģistra dzēšanas uzstādījumu HTML formu
         * 
         * @return string HTML forma
         */
        public function getFormHTML()
        {
            return view('structure.register_delete', [
                        'form_guid' => $this->form_guid,
                        'obj_id' => $this->obj_id,
                        'lists' => $this->getLists()
                    ])->render();
        }

        /**
         * Dzēš reģistru: laukus, skatus, formas
         * 
         * @return void
         */
        public function doMethod()
        {
            $this->validateData();

            try {
                DB::transaction(function ()
                {
                    DB::table('dx_db_events')->where('list_id', '=', $this->list_id)->delete();
                    
                    DB::table('dx_lists')->where('id', '=', $this->list_id)->delete();
                    DB::table('in_last_changes')->where('code', '=', 'MENU')->update(['change_time' => date('Y-n-d H:i:s')]);
                });
            }
            catch(\Exception $e) {
                Log::info("Reģistra dzēšanas kļūda: " . $e->getMessage());
                if (strpos($e->getMessage(), 'a foreign key constraint fails') !== false) {
                    throw new Exceptions\DXCustomException("Nevar dzēst reģistru, jo tas satur datus vai uz to ir atsauce citos reģistros!");
                }
                else {
                    throw $e;
                }
            }
        }

        /**
         * Pārbauda, vai norādīti obligātie lauki
         * 
         * @return void
         */
        private function validateData()
        {
            if ($this->obj_id == 0 || $this->list_id == 0) {
                throw new Exceptions\DXCustomException("Visi lauki ir obligāti jānorāda!");
            }
        }

        /**
         * Izgūst visus objekta reģistrus
         * 
         * @return Array Masīvs ar objekta reģistriem
         */
        private function getLists()
        {
            $lists = DB::table('dx_lists')->where('object_id', '=', $this->obj_id)->get();

            if (count($lists) == 0) {
                throw new Exceptions\DXCustomException("Objektam nav definēts neviens reģistrs, kuru varētu dzēst!");
            }

            return $lists;
        }

    }

}