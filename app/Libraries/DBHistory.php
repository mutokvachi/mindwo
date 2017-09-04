<?php

namespace App\Libraries
{

    use DB;
    use PDO;
    use Auth;
    use \App\Exceptions;
    use Log;

    class DBHistory
    {
        /**
         *
         * Datu bāzes ieraksta izmaiņu vēstures veidošanas klase
         *
         * Klase nodrošina ierakstu izmaiņu auditēšanu un vēstures uzkrāšanu
         */

        /**
         * Datu bāzes tabulas rindas objekts
         * 
         * @var Object 
         */
        private $db_table = null;

        /**
         * Masīvs ar formas lauku objektiem
         * 
         * @var Array 
         */
        private $fields_arr = null;

        /**
         * Masīvs ar saglabājamām datu izmaiņām
         * 
         * @var Array 
         */
        private $data_arr = array();

        /**
         * Ieraksta ID
         * 
         * @var integer 
         */
        private $item_id = 0;

        /**
         * Notikuma identifikators
         * 
         * @var integer
         */
        private $event_id = 0;
        
        /**
         * Indicates if there is at least 1 changed field on update operation
         * 
         * @var boolean
         */
        public $is_update_change = 0;

        /**
         * Array with current item data rows (used for update coparioson)
         *
         * @var array
         */
        private $current_arr = null;

        /**
         * Array with changed item fields values (used for update history saving)
         *
         * @var array
         */
        private $changes_arr = [];

        /**
         * Indicates if for data update was called comparison method compareChanges
         * This is needed because we want to eliminate risk of deadlock error so we perform queries SQLs before transaction
         *
         * @var boolean
         */
        private $is_update_compared = false;

        /**
         * Datu bāzes ieraksta izmaiņu vēstures veidošanas klases konstruktors
         * 
         * @param Object $db_table      Tabulas objekts
         * @param Object $fields_arr    Masīvs ar formas lauku objektiem
         * @param Array $data_arr       Masīvs ar labotajiem datiem
         * @param integer $item_id      Ieraksta ID
         */
        public function __construct($db_table, $fields_arr, $data_arr, $item_id)
        {
            $this->db_table = $db_table;
            $this->fields_arr = $fields_arr;
            $this->data_arr = $data_arr;
            $this->item_id = $item_id;            
        }
        
        /**
         * Returns array with list fields properties needed for history logic functionality
         * 
         * @param integer $list_id Register ID
         * @param array $arr_supported If provided then array with allowed field types. If empty then all field types are allowed
         * @return array
         */
        public static function getListFields($list_id, $arr_supported = []) {
            return DB::table('dx_lists_fields as lf')
                    ->select(
                            'lf.list_id',
                            'lf.db_name', 
                            'ft.sys_name as type_sys_name', 
                            'lf.max_lenght', 
                            'lf.is_required', 
                            'lf.default_value', 
                            'lf.title_form', 
                            'lf.title_list',
                            'lf.rel_list_id',
                            'lf_rel.db_name as rel_field_name',
                            'o_rel.db_name as rel_table_name',
                            'o_rel.is_history_logic as rel_table_is_history_logic',
                            'lf.is_public_file',
                            'lf.id as field_id',
                            'lf_rel.id as rel_field_id',
                            'l_rel.list_title as rel_list_title'
                            )
                    ->leftJoin('dx_field_types as ft', 'lf.type_id', '=', 'ft.id')
                    ->leftJoin('dx_lists_fields as lf_rel', 'lf.rel_display_field_id', '=', 'lf_rel.id')
                    ->leftJoin('dx_lists as l_rel', 'lf.rel_list_id', '=', 'l_rel.id')
                    ->leftJoin('dx_objects as o_rel', 'l_rel.object_id', '=', 'o_rel.id')
                    ->where('lf.list_id', '=', $list_id)
                    ->where(function($query) use ($arr_supported) {
                        if (count($arr_supported)) {
                            $query->whereIn('lf.type_id', $arr_supported);
                        }
                    })
                    ->whereNull('lf.formula')
                    ->get();
        }

        /**
         * Save allredy compared changes in history
         * 
         * @return void
         */
        public function makeUpdateHistory()
        {
            if ($this->db_table->is_history_logic == 0) {
                return; // not setup audit logic
            }

            if (!$this->is_update_compared) {
                throw new Exceptions\DXCustomException(trans('errors.object_update_without_compare'));
            }
            
            if (!count($this->changes_arr)) {
                return; // no changes to save/audit
            }

            $this->insertEvent(2);

            for($i=0; $i<count($this->changes_arr); $i++) {
                $this->changes_arr[$i]['event_id'] = $this->event_id;
            }

            DB::table('dx_db_history')->insert($this->changes_arr);
                      
        }

        /**
         * Auditē notikumu jauna ieraksta izveidošanai
         * 
         * @return void
         */
        public function makeInsertHistory()
        {
            if ($this->db_table->is_history_logic == 0) {
                return; // reģistram nav paredzēts auditēt datu izmaiņas
            }

            // Izveido jauna ieraksta izveidošanas notikumu
            $this->insertEvent(1);
        }

        /**
         * Auditē notikumu ieraksta dzēšanai
         * 
         * @return void
         */
        public function makeDeleteHistory()
        {
            if ($this->db_table->is_history_logic == 0) {
                return; // reģistram nav paredzēts auditēt datu izmaiņas
            }
            
            // Izveido jauna ieraksta dzēšanas notikumu
            $this->insertEvent(3);
            
            // Saglabā dzēšamā ieraksta lauku vērtības vēsturē
            $this->saveDeleted();

            DB::table('dx_db_history')->insert($this->changes_arr);
        }
        
        /**
         * Make event audit record
         * 
         * @param integer $event_type Event type (1 - new, 2 - edit, 3 - delete)
         */
        private function insertEvent($event_type)
        {
            $this->event_id = DB::table('dx_db_events')->insertGetId([
                'type_id' => $event_type,
                'user_id' => Auth::user()->id,
                'event_time' => date('Y-n-d H:i:s'),
                'list_id' => $this->db_table->list_id,
                'item_id' => $this->item_id
            ]);
        }
        
        /**
         * Uzstāda audita notikuma ieraksta ID
         * Šo izmanto, jo jauna ieraksta gadījumā vispirms notiek audits (un nav vēl zināms ievietojamā ieraksta ID)
         * 
         * @param integer $item_id Ieraksta ID
         * @return void
         */
        public function updateItemId($item_id)
        {
            if (!$this->event_id || $this->item_id > 0)
            {
                return;
            }
            
            DB::table('dx_db_events')
                    ->where('id', '=', $this->event_id)
                    ->update(['item_id' => $item_id]);
            
            $this->item_id = $item_id;                
        }

        /**
         * Atgriež masīvu ar ieraksta pašreizējiem datiem pēc norādītā ID
         * 
         * @return Array Masīvs ar ieraksta datiem
         */
        public function setCurrentData()
        {
            $sql = "SELECT * FROM " . $this->db_table->table_name . " WHERE id=" . $this->item_id;
                        
            DB::setFetchMode(PDO::FETCH_ASSOC);

            $rows = DB::select($sql);

            DB::setFetchMode(PDO::FETCH_CLASS);
            
            $this->current_arr = $rows[0];
        }

        /**
         * Salīdzina ieraksta visu lauku vērtības, atšķirības tiks ierakstītas vēstures tabulā dx_db_history
         */
        public function compareChanges()
        {            
            $this->setCurrentData();

            foreach ($this->fields_arr as $field) {
                $this->compareFieldVal($field);
            }

            $this->is_update_compared = true;
            $this->is_update_change = (count($this->changes_arr));
        }
        
        /**
         * Saglabā dzēšamā ieraksta pašreizējās vērtības vēsturē
         */
        private function saveDeleted()
        {
            if (!$this->current_arr) {
                $this->setCurrentData();
            }
            
            foreach ($this->fields_arr as $field) {
                $this->saveFieldVal($field);
            }            
        }

        /**
         * Saglabā dzēšamā ieraksta lauka vērtību vēsturē
         * 
         * @param Object $field Lauka objekts
         * @return void
         */
        private function saveFieldVal($field)
        {
            if (is_null($this->current_arr[$field->db_name]))
            {
                return;
            }
            
            $this->insertHistory($field, $this->current_arr[$field->db_name], null);
        }
        
        /**
         * Salīdzina lauka vērtības (pirms un pēc labošanas)
         * Ja vērtības atšķiras, tiks veikts ieraksts vēstures tabulā dx_db_history
         * 
         * @param Object $field Lauka objekts
         * @return void
         */
        private function compareFieldVal($field)
        {
            if (!array_key_exists(":" . $field->db_name, $this->data_arr)) {               
                return; // Lauks nav iekļauts formas datos, nav izmaiņu ko salīdzināt
            }
            
            if ($field->type_sys_name == 'datetime' && $this->current_arr[$field->db_name]) {
                // remove seconds because in UI is only minutes
                $timestamp = strtotime($this->current_arr[$field->db_name]);
                $this->current_arr[$field->db_name] = date('Y-m-d H:i', $timestamp);
            }

            if ($field->type_sys_name == 'time' && $this->current_arr[$field->db_name]) {
                // remove seconds because in UI is only minutes
                $this->current_arr[$field->db_name] = substr($this->current_arr[$field->db_name], 0, 5);
            }
            
            if ($this->data_arr[":" . $field->db_name] == $this->current_arr[$field->db_name]) { 
                if ($field->type_sys_name == 'file') {
                    $guid_name = str_replace("_name", "_guid", $field->db_name);
                    if ($this->data_arr[":" . $guid_name] == $this->current_arr[$guid_name]) {
                        return; // field is not changed
                    }
                }
                else {              
                    return; // Lauka vērtība nav mainīta
                }
            }

            if ($field->type_sys_name == 'bool') {
                $this->current_arr[$field->db_name] = ($this->current_arr[$field->db_name]) ? trans('fields.yes') : trans('fields.no');
                $this->data_arr[":" . $field->db_name] = ($this->data_arr[":" . $field->db_name]) ? trans('fields.yes') : trans('fields.no');                
            }
            
            $this->insertHistory($field, $this->current_arr[$field->db_name], $this->data_arr[":" . $field->db_name]);
        }

        /**
         * Izveido datu izmaiņu vēstures ierakstu
         * 
         * @param Object $field      Lauka objekts
         * @param mixed $old_val     Vecā vērtība
         * @param mixed $new_val     Jaunā vērtība
         */
        private function insertHistory($field, $old_val, $new_val)
        {          
            array_push($this->changes_arr, [
                'event_id' => $this->event_id,
                'field_id' => $field->field_id,
                'old_val_txt' => $this->getValTxt($field, $old_val),
                'new_val_txt' => $this->getValTxt($field, $new_val),
                'old_val_rel_id' => $this->getRelId($field, $old_val),
                'new_val_rel_id' => $this->getRelId($field, $new_val),
                'old_val_file_name' => $this->getFileName($field, 1),
                'new_val_file_name' => $this->getFileName($field, 0),
                'old_val_file_guid' => $this->getFileGuid($field, 1),
                'new_val_file_guid' => $this->getFileGuid($field, 0),
            ]);

            /*
            DB::table('dx_db_history')->insert([
                'event_id' => $this->event_id,
                'field_id' => $field->field_id,
                'old_val_txt' => $this->getValTxt($field, $old_val),
                'new_val_txt' => $this->getValTxt($field, $new_val),
                'old_val_rel_id' => $this->getRelId($field, $old_val),
                'new_val_rel_id' => $this->getRelId($field, $new_val),
                'old_val_file_name' => $this->getFileName($field, 1),
                'new_val_file_name' => $this->getFileName($field, 0),
                'old_val_file_guid' => $this->getFileGuid($field, 1),
                'new_val_file_guid' => $this->getFileGuid($field, 0),                
            ]);
            */
        }
        
        /**
         * Izgūst tekstuālo vērtību, saistītā ieraksta lauka gadījumā izgūst tekstu no saistītās tabulas
         * Ja lauks nav saistītais ieraksts, tad atgriež to pašu padoto vērtību
         * 
         * @param Object $field Lauka objekts
         * @param integer $val Saistitā ieraksta ID
         * @return string   Ieraksta tekstuālā vērtība
         */
        private function getValTxt($field, $val)
        {
            if (strlen($val) == 0)
            {
                return $val;
            }
            
            if (!$field->rel_table_name)
            {
                return $this->getDateFormated($field, $val);
            }
            
            $txt_row = DB::table($field->rel_table_name)
                        ->select(DB::raw($field->rel_field_name . " as txt"))
                        ->where('id', '=', $val)
                        ->first();
            
            if (!$txt_row) {
                return null;
            }
            
            return $txt_row->txt;            
        }
        
        /**
         * Izgūst skaitlisko ID vērtību saistītā ieraksta gadījumā
         * Ja lauks nav saistītais ieraksts, tad atgriež null
         * 
         * @param Object $field Lauka objekts
         * @param integer $val Saistītā ieraksta ID
         * @return integer Saistītā ieraksta ID
         */
        private function getRelId($field, $val)
        {
            if (!$field->rel_table_name)
            {
                return null;
            }
            else
            {
                return $val;
            }
        }
        
        /**
         * Atgriež datnes nosaukumu, ja lauka tips ir datne
         * Ja lauka tips nav datne, tad atgriež null
         * 
         * @param Object $field Lauka objekts
         * @param boolean $is_old Pazīme, vai atgriezt veco lauka vērtību (0 - veco, 1 - jauno)
         * @return string Datnes nosaukums
         */
        private function getFileName($field, $is_old)
        {
            if ($field->type_sys_name != "file")
            {
                return null;
            }
            
            $new_file_name = null;
            if (isset($this->data_arr[":" . $field->db_name]))
            {
                $new_file_name = $this->data_arr[":" . $field->db_name];
            }
            
            return ($is_old) ? $this->current_arr[$field->db_name] : $new_file_name;
        }
        
        /**
         * Atgriež datnes GUID, ja lauka tips ir datne
         * Ja lauka tips nav datne, tad atgriež null
         * 
         * @param Object $field Lauka objekts
         * @param boolean $is_old Pazīme, vai atgriezt veco lauka vērtību (0 - veco, 1 - jauno)
         * @return string Datnes GUID
         */
        private function getFileGuid($field, $is_old)
        {
            if ($field->type_sys_name != "file")
            {
                return null;
            }
            
            $guid_fld = str_replace("_name", "_guid", $field->db_name);
            
            $new_file_guid = null;
            if (isset($this->data_arr[":" . $guid_fld]))
            {
                $new_file_guid = $this->data_arr[":" . $guid_fld];
            }
            
            return ($is_old) ? $this->current_arr[$guid_fld] : $new_file_guid;
        }
        
        /**
         * Atgriež datumu/laiku tekstuālā veidā, ja lauka tips ir datums vai datums/laiks
         * Ja lauka tips nav datums/laiks, tad atgriež to pašu padoto vērtību
         * 
         * @param Object $field Lauka objekts
         * @param string $val   Vērtība
         * @return string   Vērtība vai datums/laiks formatēts tekstuālā veidā
         */
        private function getDateFormated($field, $val)
        {
            if ($field->type_sys_name != "date" && $field->type_sys_name != "datetime")
            {
                return $val;
            }
            
            return ($field->type_sys_name == "date") ? short_date($val) : long_date($val);
        }

    }

}