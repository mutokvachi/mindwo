<?php

namespace App\Libraries\Structure
{

    use Input;
    use DB;
    use App\Exceptions;

    /**
     *
     * Reģistra lauka dzēšanas klase
     *
     *
     * Objekts nodrošina lauka dzēšanu. Datu bāzē definēts CASCADE DELETE saistītajiem datiem - tādēļ tiek automātiski dzēsti saistītie ieraksti.
     * Dzēšot no saskarnes tiek kontrolētas relācijas loģiskā līmenī un pa tiešo izdzēst lauku nemaz nevar, jo vispirms būtu jādzēš skata un formas lauki
     *
     */
    class StructMethod_field_delete extends StructMethod
    {
        private $field_id = 0;
        private $list_id = 0;

        /**
         * Inicializē klases parametrus
         * 
         * @return void
         */
        public function initData()
        {
            $this->field_id = Input::get('field_id', 0);

            $this->list_id = Input::get('item_id', 0);
        }

        /**
         * Atgriež reģistra dzēšanas uzstādījumu HTML formu
         * 
         * @return string HTML forma
         */
        public function getFormHTML()
        {
            $fields_list = $this->getObjTable('dx_lists_fields');

            return view('structure.field_delete', [
                        'form_guid' => $this->form_guid,
                        'fields' => $this->getFields(),
                        'fields_list_id' => $fields_list->list_id
                    ])->render();
        }

        /**
         * Dzēš skatu un tā laukus
         * 
         * @return void
         */
        public function doMethod()
        {
            $this->validateData();

            DB::transaction(function ()
            {
                DB::table('dx_lists_fields')->where('id', '=', $this->field_id)->delete();
            });
        }

        /**
         * Pārbauda, vai norādīti obligātie lauki
         * 
         * @return void
         */
        private function validateData()
        {
            if ($this->field_id == 0) {
                throw new Exceptions\DXCustomException("Visi lauki ir obligāti jānorāda!");
            }
        }

        /**
         * Izgūst visus reģistra laukus
         * 
         * @return Array Masīvs ar reģistra laukiem
         */
        private function getFields()
        {
            $fields = DB::table('dx_lists_fields')->where('list_id', '=', $this->list_id)->orderBy('title_list', 'ASC')->get();

            if (count($fields) == 0) {
                throw new Exceptions\DXCustomException("Reģistram nav definēts neviens lauks, kuru varētu dzēst!");
            }

            return $fields;
        }

    }

}