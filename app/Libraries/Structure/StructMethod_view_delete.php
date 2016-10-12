<?php

namespace App\Libraries\Structure
{

    use Input;
    use DB;
    use App\Exceptions;

    class StructMethod_view_delete extends StructMethod
    {
        /**
          *
          * Skata dzēšanas klase
          *
          *
          * Objekts nodrošina skata dzēšanu. Datu bāzē definēts CASCADE DELETE saistītajiem datiem - tādēļ tiek automātiski dzēsti saistītie ieraksti.
          * Dzēšot no saskarnes tiek kontrolētas relācijas loģiskā līmenī un pa tiešo izdzēst skatu nemaz nevar, jo vispirms būtu jādzēš skata lauki
          *
         */

        private $view_id = 0;
        private $list_id = 0;

        /**
         * Inicializē klases parametrus
         * 
         * @return void
         */

        public function initData()
        {
            $this->view_id = Input::get('view_id', 0);
            
            $this->list_id = Input::get('item_id', 0);
        }

        /**
         * Atgriež reģistra dzēšanas uzstādījumu HTML formu
         * 
         * @return string HTML forma
         */

        public function getFormHTML()
        {
            $views_list = $this->getObjTable('dx_views');
            
            return view('structure.view_delete', [
                        'form_guid' => $this->form_guid,
                        'views' => $this->getViews(),
                        'views_list_id' => $views_list->list_id
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
                DB::table('dx_views')->where('id', '=', $this->view_id)->delete();
            });
        }

        /**
         * Pārbauda, vai norādīti obligātie lauki
         * 
         * @return void
         */

        private function validateData()
        {
            if ($this->view_id == 0)
            {
                throw new Exceptions\DXCustomException("Visi lauki ir obligāti jānorāda!");
            }
        }

         /**
         * Izgūst visus reģistra skatus
         * 
         * @return Array Masīvs ar reģistra skatiem
         */

        private function getViews()
        {
            $views = DB::table('dx_views')->where('list_id', '=', $this->list_id)->orderBy('title', 'ASC')->get();

            if (count($views) == 0)
            {
                throw new Exceptions\DXCustomException("Reģistram nav definēts neviens skats, kuru varētu dzēst!");
            }

            return $views;
        }

    }

}