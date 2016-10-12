<?php

namespace App\Libraries\Structure
{

    use Input;
    use Illuminate\Database\Schema\Blueprint;
    use \Illuminate\Support\Facades\Schema;
    use App\Exceptions;
    use DB;
    
    class StructMethod_generate_audit extends StructMethod
    {
        /**
         *
         * Vēstures veidošanas iestatīšanas klase
         *
         * Pārbauda, kurām tabulām nav lietotāja izmaiņu lauki un tos pieliek, iestata pazīmi pie objektiem, ka ir jāauditē izmaiņas
         *
         */

        /**
         * Norāda tabulu nosaukumus atdalītus ar komatu, kas nav jāņem vērā (nav nepieciešama auditācija)
         * @var type 
         */
        private $exclude_tables = "'jobs', 'migrations', 'in_visit_log', 'failed_jobs', 'dx_db_history'";

        /**
         * SVS objekta tabulas nosaukums
         * @var string 
         */
        private $obj_db_name = "";

        /**
         * SVS objekta ID
         * 
         * @var integer
         */
        private $obj_id = 0;
        
        /**
         * Inicializē klases parametrus
         * 
         * @return void
         */
        public function initData()
        {
            $this->obj_id = Input::get('obj_id', 0);

            if ($this->obj_id > 0) {
                $this->setObjectTable();
            }
        }

        /**
         * Atgriež audita iestatīšanas formu
         * 
         * @return string HTML forma
         */
        public function getFormHTML()
        {
            return view('structure.object_history', [
                        'form_guid' => $this->form_guid,
                        'ignore_tables' => str_replace("'","",$this->exclude_tables)
                    ])->render();
        }

        /**
         * Ģenerē vēstures veidošanu
         * 
         * @return void
         */
        public function doMethod()
        {
            DB::transaction(function ()
            {
                $this->addHistoryToTables();
            });
        }

        /**
         * Uzstāda datu objekta tabulas nosaukumu
         * 
         * @throws Exceptions\DXCustomException
         */
        private function setObjectTable()
        {
            $obj_row = DB::table('dx_objects')->where('id', '=', $this->obj_id)->first();
            
            if (!$obj_row)
            {
                throw new Exceptions\DXCustomException("Nav atrodams objekts ar ID " . $this->obj_id . "!");
            }
            
            $this->obj_db_name = $obj_row->db_name;
        }
        
        /**
         * Izgūst tabulas, kurām nav vēstures veidošana un pievieno tām vēstures veidošanas laukus
         */
        private function addHistoryToTables()
        {
            $tables = $this->getTablesList();

            foreach ($tables as $table) {
                $this->addHistoryFields($table->TABLE_NAME);

                $this->enableObjectHistory($table->TABLE_NAME);
            }
        }

        /**
         * Pievieno norādītajai tabulai 4 vēstures veidošanas laukus.
         * Lietotāju (izveidotāju/rediģētāju) un datumus (izveidošanas/labošanas)
         * 
         * @param string $table_name Tabulas nosaukums
         */
        private function addHistoryFields($table_name)
        {            
            Schema::table($table_name, function (Blueprint $table)
            {
                $table->integer('created_user_id')->nullable();
                $table->datetime('created_time')->nullable();
                $table->integer('modified_user_id')->nullable();
                $table->datetime('modified_time')->nullable();
            });
        }

        /**
         * Iespējo SVS definētajam objektam vēstures veidošanu
         * 
         * @param string $table_name Tabulas nosaukums
         */
        private function enableObjectHistory($table_name)
        {
            DB::table('dx_objects')->where('db_name', '=', $table_name)->update(['is_history_logic' => 1]);
        }

        /**
         * Izgūst masīvu ar tabulu nosaukumiem, kurām nav darbību vēstures lauki
         * 
         * @return Array Masīvs ar tabulu nosaukumiem
         */
        private function getTablesList()
        {
            $sql = "SELECT 
                        t.TABLE_NAME 
                    FROM 
                        INFORMATION_SCHEMA.TABLES t
                        LEFT JOIN 
                            (
                            SELECT DISTINCT 
                                TABLE_NAME 
                            FROM 
                                INFORMATION_SCHEMA.COLUMNS 
                            WHERE 
                                TABLE_SCHEMA = '" . env('DB_DATABASE', 'not_set') . "'
                                AND COLUMN_NAME='created_user_id'
                            ) c on t.TABLE_NAME = c.TABLE_NAME
                    WHERE 
                        c.TABLE_NAME is null 
                        AND t.TABLE_SCHEMA = '" . env('DB_DATABASE', 'not_set') . "'
                        AND t.TABLE_NAME not in (" . $this->exclude_tables . ")";

            return DB::select($sql);
        }

    }

}