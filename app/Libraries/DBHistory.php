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
        private $is_update_change = 0;

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
         * Izveido ieraksta vēsturi un auditē veiktās izmaiņas datu labošanas gadījumā
         * 
         * @return void
         */
        public function makeUpdateHistory()
        {
            if ($this->db_table->is_history_logic == 0) {
                return; // reģistram nav paredzēts auditēt datu izmaiņas
            }

            DB::transaction(function () {
                
                // Izveido rediģēšanas notikumu
                $this->insertEvent(2);

                // Salīdzina lauku izmaiņas un saglabā vēsturē mainītās vērtības
                $this->compareChanges();
                
                if (!$this->is_update_change) {
                    throw new Exceptions\DXCustomException(trans('errors.nothing_changed'));
                }
            });
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
        }
        
        /**
         * 
         * @param integer $event_type Notikuma veids (1 - jauns, 2 - rediģēšana, 3 - dzēšana)
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
        private function getCurrentData()
        {
            $sql = "SELECT * FROM " . $this->db_table->table_name . " WHERE id=" . $this->item_id;
                        
            DB::setFetchMode(PDO::FETCH_ASSOC);

            $rows = DB::select($sql);

            DB::setFetchMode(PDO::FETCH_CLASS);
            
            return $rows[0];
        }

        /**
         * Salīdzina ieraksta visu lauku vērtības, atšķirības tiks ierakstītas vēstures tabulā dx_db_history
         */
        private function compareChanges()
        {
            $current_arr = $this->getCurrentData();

            foreach ($this->fields_arr as $field) {
                $this->compareFieldVal($field, $current_arr);
            }
        }
        
        /**
         * Saglabā dzēšamā ieraksta pašreizējās vērtības vēsturē
         */
        private function saveDeleted()
        {
            $current_arr = $this->getCurrentData();

            foreach ($this->fields_arr as $field) {
                $this->saveFieldVal($field, $current_arr);
            }
        }

        /**
         * Saglabā dzēšamā ieraksta lauka vērtību vēsturē
         * 
         * @param Object $field Lauka objekts
         * @param Array $current_arr Ieraksta visu lauku vērtību masīvs atbilstoši datu ievades formai
         * @return void
         */
        private function saveFieldVal($field, $current_arr)
        {
            if (is_null($current_arr[$field->db_name]))
            {
                return;
            }
            
            $this->insertHistory($field, $current_arr[$field->db_name], null, $current_arr);
        }
        
        /**
         * Salīdzina lauka vērtības (pirms un pēc labošanas)
         * Ja vērtības atšķiras, tiks veikts ieraksts vēstures tabulā dx_db_history
         * 
         * @param Object $field Lauka objekts
         * @param Array $current_arr Ieraksta visu lauku vērtību masīvs atbilstoši datu ievades formai
         * @return void
         */
        private function compareFieldVal($field, $current_arr)
        {
            Log::info("FLD: " . $field->db_name);
            if (!array_key_exists(":" . $field->db_name, $this->data_arr)) {
                Log::info("NOT set");
                return; // Lauks nav iekļauts formas datos, nav izmaiņu ko salīdzināt
            }
            
            if ($this->data_arr[":" . $field->db_name] == $current_arr[$field->db_name]) {
                Log::info("not changed | " . $this->data_arr[":" . $field->db_name] . " | "  . $current_arr[$field->db_name]);
                return; // Lauka vērtība nav mainīta
            }

            $this->insertHistory($field, $current_arr[$field->db_name], $this->data_arr[":" . $field->db_name], $current_arr);
            
            $this->is_update_change = 1;
        }

        /**
         * Izveido datu izmaiņu vēstures ierakstu
         * 
         * @param Object $field      Lauka objekts
         * @param mixed $old_val     Vecā vērtība
         * @param mixed $new_val     Jaunā vērtība
         * @param Array $current_arr Ieraksta visu lauku vērtību masīvs atbilstoši datu ievades formai
         */
        private function insertHistory($field, $old_val, $new_val, $current_arr)
        {            
            DB::table('dx_db_history')->insert([
                'event_id' => $this->event_id,
                'field_id' => $field->field_id,
                'old_val_txt' => $this->getValTxt($field, $old_val),
                'new_val_txt' => $this->getValTxt($field, $new_val),
                'old_val_rel_id' => $this->getRelId($field, $old_val),
                'new_val_rel_id' => $this->getRelId($field, $new_val),
                'old_val_file_name' => $this->getFileName($field, 1, $current_arr),
                'new_val_file_name' => $this->getFileName($field, 0, $current_arr),
                'old_val_file_guid' => $this->getFileGuid($field, 1, $current_arr),
                'new_val_file_guid' => $this->getFileGuid($field, 0, $current_arr),
                
            ]);
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
         * @param Array $current_arr Ieraksta visu lauku vērtību masīvs atbilstoši datu ievades formai
         * @return string Datnes nosaukums
         */
        private function getFileName($field, $is_old, $current_arr)
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
            
            return ($is_old) ? $current_arr[$field->db_name] : $new_file_name;
        }
        
        /**
         * Atgriež datnes GUID, ja lauka tips ir datne
         * Ja lauka tips nav datne, tad atgriež null
         * 
         * @param Object $field Lauka objekts
         * @param boolean $is_old Pazīme, vai atgriezt veco lauka vērtību (0 - veco, 1 - jauno)
         * @param Array $current_arr Ieraksta visu lauku vērtību masīvs atbilstoši datu ievades formai
         * @return string Datnes GUID
         */
        private function getFileGuid($field, $is_old, $current_arr)
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
            
            return ($is_old) ? $current_arr[$guid_fld] : $new_file_guid;
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