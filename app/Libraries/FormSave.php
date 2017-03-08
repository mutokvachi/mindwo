<?php

namespace App\Libraries
{

    use DB;
    use Auth;
    use \App\Exceptions;
    use App\Libraries\DBHistory;
    use Log;
    use App\Libraries\FormsActions;
    
    class FormSave
    {
        /**
         * Klase nodrošina jaunu un labotu ierakstu saglabāšanu
         */

        /**
         * Labojamā vai jaunā ieraksta ID
         * 
         * @var integer 
         */
        public $item_id = 0;

        /**
         * Masīvā tiek ievietoti masīvi ar lauks->tekstuālā vērtība atbilstībā.
         * Tas tiek izmantots HTML pusē, kad pēc formas saglabāšanas tiek atjaunināti lauku tekstuālās vērtības, 
         * piemēram, šodienas datuma noģenerēšana, reģ. nr. noģenerēšana u.c.)
         * 
         * @var Array 
         */
        public $gener_arr = array();

        /**
         * Masīvs ar saistīto ierakstu sinhronizēšanas norādījumiem (tabula, lauks)
         * Tas nepieciešams lai, piemēram, atbildes un nosūtāmo dokumentu gadījumā, viens otram uzstādītu vērtības
         * Bet šajā klasē, vēl ieraksts var būt arī bez ID - tāpēc klasei definēts šis parametrs un sasinhronizēšana tiks veikta vēlāk
         * 
         * @var Array 
         */
        private $upd_rel_arr = array();
        
        /**
         * Atjaunināmā lauka ID.
         * Forma var tikt izsaukta no citas formas lookup lauka, nopiežot, piemēram, jauna ieraksta pievienošanu
         * Tādēļ ir nepieciešams pēc izmaiņu veikšanas, atjaunināt lauka vērtību saistītajā formā
         * 
         * @var integer 
         */
        public $call_field_id = 0;

        public $call_field_htm_id = "";
        
        /**
         * JavaScript izteiksme, kurā parasti padod HTML formas elementa vērtības uzstādīšanas skriptu
         * Šis parametrs ir kombinācijā ar $call_field_id, pie kura skatīt detalizētāku aprakstu
         * 
         * @var string 
         */
        public $call_field_type = "";

        /**
         * Vērtība, kura tiks atgriezta saistītajai formai
         * Šis parametrs ir kombinācijā ar $call_field_id, pie kura skatīt detalizētāku aprakstu
         * 
         * @var string 
         */
        public $call_field_value = "";

        /**
         * Reģistra datu labošanas formas ID
         * 
         * @var integer 
         */
        private $form_id = 0;

        /**
         * Ja reģistra dati glabājas denormalizētā tabulā, kurā tiek glabāti n reģistru dati, tad šajā parametrā norāda reģistra ID.
         * 
         * @var type 
         */
        private $multi_list_id = 0;

        /**
         * POST pieprasījuma objekts
         * 
         * @var Request 
         */
        private $request = null;

        /**
         * Izpildāmā datu labošanas vai jauna ieraksta SQL izteiksme.
         * Izteiksme tiek izveidota ar klases loģiku.
         * 
         * @var string 
         */
        private $sql = "";

        /**
         * SQL izteiksmes daļa ar lauku nosaukumiem.
         * Izteiksme tiek izveidota ar klases loģiku.
         * 
         * @var string 
         */
        private $sql_f = "";

        /**
         * SQL izteiksmes daļa ar lauku vērtībām
         * 
         * @var string 
         */
        private $sql_v = "";

        /**
         * Masīvs, kurā tiek ievietotas lauku vērtības.
         * Masīvu izmanto kā parametru izpildot datu saglabāšanas SQL izteiksmi
         * 
         * @var Array 
         */
        private $arr = array();

        /**
         * Izmaiņu auditēšanas objekts
         * 
         * @var type 
         */
        private $history = null;

        /**
         * Formas saglabāšanas klases konsturktors
         * 
         * @param Request $request POST/GET pieprasījuma objekts
         */
        public function __construct($request)
        {
            $this->form_id = $request->input('edit_form_id');
            $this->item_id = $request->input('item_id', 0);
            $this->multi_list_id = $request->input('multi_list_id', 0);

            $this->request = $request;

            $this->call_field_id = $request->input('call_field_id', 0);

            $this->saveData();
        }

        /**
         * Validē un saglabā formas lauku vērtības datu bāzē
         */
        private function saveData()
        {
            $multi_fields = array();

            if ($this->item_id == 0) {
                // multi lauku saglabāšana iespējama tikai jauniem ierakstiem
                $multi_fields = $this->getFormsFields(1, $this->form_id);
            }

            if (count($multi_fields) == 0) {
                $this->processAllSingleVals();
            }
            else if (count($multi_fields) == 1) {
                $this->processMultiVals($multi_fields[0]);
            }
            else {
                throw new Exceptions\DXCustomException("Vienā formā nav pieļaujams ievietot vairāk kā vienu lauku, kuram iespējamas vairākas vērtības vienlaicīgi!");
            }
        }

        /**
         * Veic datu saglabāšanu - vairākiem ierakstiem, vienā laukā norādītas n vērtības (piemēram, vairāku attēlu augšuplāde)
         * 
         * @param Array $multi_field Masīvs ar formas lauku, kurā var būt vairākas vērtības (parasti attēla lauks)
         */
        private function processMultiVals($multi_field)
        {
            $fields = $this->getFormsFields(0, $this->form_id);

            DB::transaction(function () use ($fields, $multi_field)
            {
                $fld_save_multi = FieldsSave\FieldSaveFactory::build_field($this->request, $multi_field, $this->item_id);

                foreach ($fld_save_multi->getVal() as $val_multi) {
                    $this->resetGlobals();

                    foreach ($val_multi as $key => $val) {
                        $this->arr[":" . $key] = $val;
                        $this->prepareFieldSQL($key);
                    }

                    $this->fillFieldsVals($fields);

                    $this->prepareTableSQL($fields);

                    $this->executeSQL();
                    
                    $this->executeActions(2);
                }
            });
        }

        /**
         * Notīra klases parametru vērtības
         * Šo izmanto, ja tiek saglabāti n jauni ieraksti vienā transakcijā
         */
        private function resetGlobals()
        {
            $this->arr = array();
            $this->gener_arr = array();
            $this->call_field_type = "";
            $this->call_field_value = "";
            $this->call_field_id = 0;
            $this->call_field_htm_id = "";
            $this->sql = ""; // Compleated SQL expression to be executed
            $this->sql_f = ""; // SQL part for field's names
            $this->sql_v = "";
            $this->item_id = 0;
            $this->history = null;
            $this->upd_rel_arr = array();
        }

        /**
         * Veic datu saglabāšanu - vienam ierakstam
         */
        private function processAllSingleVals()
        {
            $this->executeActions(0);
            
            $fields = $this->getFormsFields(-1, $this->form_id);

            DB::transaction(function () use ($fields)
            {
                $this->fillFieldsVals($fields);

                $this->prepareTableSQL($fields);

                $this->executeSQL();
                
                $this->executeActions(1);
            });
        }
        
        /**
         * Executes custom actions if cush are defined for form
         * We have 2 types: before and after forms saving actions
         * 
         * @param integer $action_type Indicates what action types should be executed (0 - before save, 1 - after save, 2 - all)
         */
        private function executeActions($action_type) {
            $actions = DB::table('dx_forms_actions as fa')
                       ->select('a.code')
                       ->join('dx_actions as a', 'fa.action_id', '=', 'a.id')
                       ->where(function($query) use ($action_type) {
                           if ($action_type != 2) {
                               $query->where('fa.is_after_save', '=', $action_type);
                           }
                       })
                       ->where('fa.form_id', '=', $this->form_id)
                       ->get();
            
            foreach($actions as $action) {
                $act = FormsActions\ActionFactory::build_action($this->request, $this->item_id, $action->code);
            }
        }

        /**
         * Aizpilda klases masīvu $this->arr ar lauku vērtībām
         * 
         * @param Array $fields Masīvs ar formas laukiem
         */
        private function fillFieldsVals($fields)
        {
            foreach ($fields as $fld) {
                if ($fld->is_readonly == 1) {
                    $this->setDefaultValue($fld);
                }
                else {
                    $this->setFieldValue($fld);
                }
            }

            if ($this->multi_list_id > 0) {
                $this->arr[":multi_list_id"] = $this->multi_list_id;
                $this->prepareFieldSQL("multi_list_id");
            }
        }

        /**
         * Uzstāda lauka noklusēto vērtību
         * 
         * @param Object $fld Lauka objekts
         */
        private function setDefaultValue($fld)
        {
            if ($this->item_id == 0 && strlen($fld->default_value) > 0) {
                
                $val = $fld->default_value;
                
                if ($val=="[ME]") {
                    $val = Auth::user()->id;
                }
                
                if ($val=="[NOW]") {
                    $val = date('Y-n-d H:i:s');
                }
                
                $this->arr[":" . $fld->db_name] = $val;
                $this->prepareFieldSQL($fld->db_name);
            }
        }

        /**
         * Uzstāda lauka vērtību
         * 
         * @param Object $fld Lauka objekts
         */
        private function setFieldValue($fld)
        {
            $fld_save = FieldsSave\FieldSaveFactory::build_field($this->request, $fld, $this->item_id);

            foreach ($fld_save->getVal() as $key => $val) {
                $this->arr[":" . $key] = $val;
                $this->prepareFieldSQL($key);

                if ($fld->field_id == $this->call_field_id) {
                    $this->call_field_htm_id = $this->request->input('call_field_htm_id', "");
                    $this->call_field_type = $this->request->input('call_field_type', "");
                    $this->call_field_value = $val;
                }
            }

            if (count($fld_save->getTxtArr()) > 0) {
                $this->gener_arr[count($this->gener_arr)] = $fld_save->getTxtArr();
            }
            
            if (count($fld_save->upd_rel_arr) > 0) {
                foreach($fld_save->upd_rel_arr as $upd_item) {
                    array_push($this->upd_rel_arr, $upd_item);
                }
            }
        }

        /**
         * Izpilda formas saglabāšanas SQL - jaunam ierakstam vai esoša labošanai
         * 
         * @throws Exceptions\DXCustomException
         * @throws \App\Libraries\Exception
         */
        private function executeSQL()
        {            
            try {
                if ($this->item_id == 0) {
                    DB::insert($this->sql, $this->arr);
                    $this->item_id = DB::getPdo()->lastInsertId();

                    $this->history->updateItemId($this->item_id);
                }
                else {
                    $affected = DB::update($this->sql, $this->arr);

                    if ($affected == 0) {
                        throw new Exceptions\DXCustomException(trans('errors.nothing_changed'));
                    }
                }
                
                $this->synchroRelFields();
            }
            catch (\Exception $e) {
                if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    throw new Exceptions\DXCustomException(trans('errors.must_be_uniq'));
                }
                else {
                    throw $e;
                }
            }
        }
        
        /**
         * Sinhronizē saistītā ieraksta reģistra lauku (abpusēji saistīto ierakstu gadījumā, piemēram, saņemtais un atbildes dokumenti)
         */
        private function synchroRelFields() {
            if (count($this->upd_rel_arr) == 0) {
                return;
            }
            
            foreach ($this->upd_rel_arr as $rel_upd) {
                
                $upd_val = ($rel_upd['oper'] == 'null') ? null : $this->item_id;
                
                DB::table($rel_upd['table'])
                ->where('id', '=', $rel_upd['id'])
                ->update([$rel_upd['field'] => $upd_val]);
            }
        }

        /**
         * Sagatavo formas datu saglabāšanas SQL izteiksmi (INSERT vai UPDATE - atkarībā no tā, vai uzstādīts item_id)
         * 
         * @param Array $fields Masīvs ar formas lauku objektiem
         */
        private function prepareTableSQL($fields)
        {
            $tbl = $this->getFormTable($this->form_id);
            $time_now = date('Y-n-d H:i:s');

            $this->history = new DBHistory($tbl, $fields, $this->arr, $this->item_id);

            if ($this->item_id == 0) {
                $this->history->makeInsertHistory(); // obligāti izpildam vispirms, jo citādi masīvā var ierakstīt arī lietotāja laukus, kas veic darbību
                $this->sql = $this->getInsertSQL($tbl, $time_now);
            }
            else {
                $this->history->makeUpdateHistory(); // obligāti izpildam vispirms, jo citādi masīvā var ierakstīt arī lietotāja laukus, kas veic darbību
                $this->sql = $this->getUpdateSQL($tbl, $time_now);
            }
        }

        /**
         * Izveido SQL UPDATE izteiksmi
         * 
         * @param Object $tbl Tabulas objekts
         * @param DateTime $time_now Šodienas datums/laiks
         * @return string SQL UPDATE izteiksme
         */
        private function getUpdateSQL($tbl, $time_now)
        {
            $sql = "UPDATE " . $tbl->table_name . " SET ";

            if ($tbl->is_history_logic == 1) {
                $this->prepareUpdate("modified_user_id");
                $this->prepareUpdate("modified_time");

                $this->arr[":modified_user_id"] = Auth::user()->id;
                $this->arr[":modified_time"] = $time_now;
            }

            return $sql . $this->sql_f . " WHERE id = " . $this->item_id;
        }

        /**
         * Izveido SQL INSERT izteiksmi
         * 
         * @param Object $tbl Tabulas objekts
         * @param DateTime $time_now Šodienas datums/laiks
         * @return string SQL INSERT izteiksme
         */
        private function getInsertSQL($tbl, $time_now)
        {
            $sql = "INSERT INTO " . $tbl->table_name . "(";

            if ($tbl->is_history_logic == 1) {
                $this->prepareInsert("created_user_id");
                $this->prepareInsert("created_time");
                $this->prepareInsert("modified_user_id");
                $this->prepareInsert("modified_time");

                $this->arr[":created_user_id"] = Auth::user()->id;
                $this->arr[":created_time"] = $time_now;
                $this->arr[":modified_user_id"] = Auth::user()->id;
                $this->arr[":modified_time"] = $time_now;
            }

            return $sql . $this->sql_f . ") VALUES (" . $this->sql_v . ")";
        }

        /**
         * Pievieno formas lauku SQL izteiksmei
         * 
         * @param string $fld_name Lauka nosaukums datu bāzē
         */
        private function prepareFieldSQL($fld_name)
        {
            if ($this->item_id == 0) {
                $this->prepareInsert($fld_name);
            }
            else {
                $this->prepareUpdate($fld_name);
            }
        }

        /**
         * Pievieno lauku SQL INSERT izteiksmei
         * 
         * @param string $fld_name Lauka nosaukums datu bāzē
         */
        private function prepareInsert($fld_name)
        {
            if (strlen($this->sql_f) > 0) {
                $this->sql_f = $this->sql_f . ", ";
                $this->sql_v = $this->sql_v . ", ";
            }

            $this->sql_f = $this->sql_f . $fld_name;
            $this->sql_v = $this->sql_v . ":" . $fld_name;
        }

        /**
         * Pievieno lauku SQL UPDATE izteiksmei
         * 
         * @param string  $fld_name Lauka nosaukums datu bāzē
         */
        private function prepareUpdate($fld_name)
        {
            if (strlen($this->sql_f) > 0) {
                $this->sql_f = $this->sql_f . ", ";
            }

            $this->sql_f = $this->sql_f . $fld_name . " = :" . $fld_name;
        }

        /**
         * Izgūst formas lauku masīvu
         * 
         * @param integer $is_multi_field Pazīme, kādus lauku atlasīt (-1: visus, 0 - tos, kas nav multi lauki, 1 - tos, kas ir multi lauki)
         * @param integer $form_id        Reģistra formas ID
         * @return Array Masīvs ar formas laukiem
         * @throws Exceptions\DXNoFormFieldFoundException
         */
        public static function getFormsFields($is_multi_field = -1, $form_id)
        {
            $sql = "
            SELECT
                    lf.id as field_id,
                    ff.is_hidden,
                    lf.db_name,
                    ft.sys_name as type_sys_name,
                    lf.title_form,
                    lf.max_lenght,
                    lf.is_required,
                    ff.is_readonly,
                    o.db_name as table_name,
                    lf.rel_list_id,
                    lf_rel.db_name as rel_field_name,
                    o_rel.db_name as rel_table_name,
                    o.is_history_logic,
                    lf.is_public_file,
                    lf.numerator_id,
                    lf.default_value,
                    ff.is_readonly,
                    lf.is_clean_html,
                    lf.is_text_extract,
                    lf.is_fields_synchro,
                    lf.is_manual_reg_nr,
                    lf.reg_role_id,
                    lf.list_id,
                    lf.is_image_file
            FROM
                    dx_forms_fields ff
                    inner join dx_lists_fields lf on ff.field_id = lf.id
                    inner join dx_field_types ft on lf.type_id = ft.id
                    inner join dx_forms f on ff.form_id = f.id
                    inner join dx_lists l on f.list_id = l.id
                    inner join dx_objects o on l.object_id = o.id
                    left join dx_lists l_rel on lf.rel_list_id = l_rel.id
                    left join dx_objects o_rel on l_rel.object_id = o_rel.id
                    left join dx_lists_fields lf_rel on lf.rel_display_field_id = lf_rel.id
            WHERE
                    ff.form_id = :form_id
                    AND lf.db_name not in('id', 'created_user_id', 'modified_user_id', 'created_time', 'modified_time')
            ";

            if ($is_multi_field != -1) {
                $sql .= " AND lf.is_multiple_files = " . $is_multi_field;
            }

            $sql .= " 
            ORDER BY
                    ff.order_index
            ";

            $fields = DB::select($sql, array('form_id' => $form_id));

            if (count($fields) == 0 && $is_multi_field == -1) {
                throw new Exceptions\DXCustomException("Formai ar ID " . $form_id . " nav definēts neviens datu ievades lauks!");
            }

            return $fields;
        }

        /**
         * Izgūst formas reģistra tabulas objektu
         * 
         * @param integer $form_id Formas id (no tabulas dx_forms)
         * @return Object Formas reģistra tabulas objekts
         * @throws Exceptions\DXCustomException
         */
        public static function getFormTable($form_id)
        {
            $sql = "
            SELECT
                    o.db_name as table_name,
                    o.is_history_logic,
                    f.list_id
            FROM                    
                    dx_forms f
                    inner join dx_lists l on f.list_id = l.id
                    inner join dx_objects o on l.object_id = o.id                    
            WHERE
                    f.id = :form_id
            ";

            $rows = DB::select($sql, array('form_id' => $form_id));

            if (count($rows) == 0) {
                throw new Exceptions\DXCustomException("Object not found for the form with ID = " . $form_id);
            }

            return $rows[0];
        }

    }

}