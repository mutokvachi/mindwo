<?php

namespace App\Libraries\FormsActions
{

    use \App\Exceptions;
    use DB;

    /**
     * Sets department and unit acording to document creator
     * This action is designed for using on table dx_doc
     * 
     * Department field - source_id
     * Unit field - pages_count
     * Document creator field - perform_empl_id
     */
    class Action_SET_DEP_UNIT extends Action
    {

        /**
         * Sets db_table_name parameter
         */
        public function setTableName()
        {
            $this->db_table_name = ["dx_doc"];
        }

        /**
         * Performs action
         */
        public function performAction()
        {
            if (!$this->item_id) {
                throw new Exceptions\DXCustomException("Sistēmas konfigurācijas kļūda! Struktūrvienības uzstādīšanas aktivitāti pieļaujams izsaukt tikai pēc ieraksta saglabāšanas.");
            }
            $creator_id = $this->request->input('perform_empl_id', 0);

            if ($creator_id == 0) {
                throw new Exceptions\DXCustomException("Ir obligāti jābūt norādītam sagatavotājam! Datus nav iespējams saglabāt.");
            }

            $empl_row = DB::table('dx_users')->where('id', '=', $creator_id)->first();

            if (!$empl_row->department_id) {
                throw new Exceptions\DXCustomException("Sagatavotāja darbinieka profilā nav norādīta struktūrvienība! Datus nav iespējams saglabāt.");
            }
            $dep_row = DB::table('in_departments')->where('id', '=', $empl_row->department_id)->first();

            DB::table('dx_doc')->where('id', '=', $this->item_id)->update([
                'source_id' => $dep_row->source_id,
                'pages_count' => $empl_row->department_id
            ]);
        }

    }

}