<?php
namespace App\Libraries 
{
    use DB;
    use Log;
    use Auth;
    use PDO;
    use App\Libraries\Rights;
    use App\Exceptions;
    use App\Libraries\FieldsHtm;
    
    class FormField
    { 
        /**
         * Masīvs ar lauka atribūtiem
         * 
         * @var Array 
         */
        private $fld_attr = null;
        
        /**
         * Ieraksta ID. Ja rediģēšana, tad tas ir lielāks par 0
         * 
         * @var integer 
         */
        private $item_id = 0;
        
        /**
         * Ieraksta reģistra ID
         * @var integer
         */
        private $list_id = 0;
        
        private $item_field;
        private $parent_item_id = 0;
        private $item_value;
	private $is_hidden_field = 0;
        private $parent_field_id = 0;                
        private $data_row = null;
        private $is_sub_grid_form = 0;
        private $frm_uniq_id;
        
        private $tree_full_path = "";
        
        public $is_disabled_mode = 0;
        public $fld_height = 0;
        
        /**
        * Pazīme, vai ieraksta neatrodas darbplūsmā un ja atrodas, vai lietotājam ir rediģēšanas uzdevums, kas pieļauj rediģēt šo ierakstu
        * 
        * @var boolean
        */
        public $is_editable_wf = false;
        
        // It is expected that binded field follows parent and no other bindings between
        public $binded_field_id = 0;
        public $binded_rel_field_id = 0;
        public $binded_rel_field_value = 0;
        
        public function __construct($fld_params, $list_id, $item_id, $parent_item_id, $parent_field_id, $row_data, $frm_uniq_id)
        {                
            $this->fld_attr = $fld_params;
            $this->item_id = $item_id;
            $this->parent_item_id = $parent_item_id;
            $this->parent_field_id = $parent_field_id;
            $this->data_row = $row_data;
            $this->frm_uniq_id = $frm_uniq_id;
            
            $this->item_field = $this->fld_attr->db_name;
            $this->list_id = $list_id;
            
            $this->set_field_core_properties();
        }
        
        public function get_field_htm()
        {                                    
            if ($this->parent_item_id > 0 && $this->fld_attr->field_id == $this->parent_field_id)
            {
                // Form is opened from sub-grid
                // We dont show related form's element
                //$this->is_hidden_field = 1;
                $this->fld_attr->is_readonly = 1;
                $this->is_hidden_field = 0;
            }
            
            if ($this->is_hidden_field == 0)
            {
                $fld_height = $this->fld_attr->height_px + 10;
                
                $field_htm = FieldsHtm\FieldHtmFactory::build_field($this->fld_attr, $this->item_id, $this->item_value, $this->list_id, $this->frm_uniq_id, $this->is_disabled_mode);
                
                if (property_exists($field_htm, "binded_field_id") && $this->binded_field_id > 0) {                    
                    $field_htm->binded_field_id = $this->binded_field_id;
                    $field_htm->binded_rel_field_id = $this->binded_rel_field_id;
                    $field_htm->binded_rel_field_value = $this->binded_rel_field_value;
                }
                
                if (property_exists($field_htm, "value_guid")) {
                    $field_htm->value_guid = $this->data_row[str_replace('_name', '_guid', $this->fld_attr->db_name)];
                }
                
                $htm = $field_htm->getHtm();
                
                // šim vienmēr jāizpildās pēc getHtm metodes, jo tajā metodē uzstāda papildus parametrus
                if (property_exists($field_htm, "binded_field_id") && $this->binded_field_id == 0) {
                    $this->binded_field_id = $field_htm->binded_field_id;
                    $this->binded_rel_field_id = $field_htm->binded_rel_field_id;
                    $this->binded_rel_field_value = $field_htm->binded_rel_field_value;
                }
                
                if (strlen($htm) > 0) {
                    return view('fields.visible', [
                        'label_title' => $this->fld_attr->title_form, 
                        'is_readonly' => $this->fld_attr->is_readonly, 
                        'item_value' => $this->item_value, 
                        'item_htm' => $htm,
                        'is_required' => $this->fld_attr->is_required,
                        'hint' => $this->fld_attr->hint,
                        'fld_name' => $this->fld_attr->db_name,
                        'group_label' => $this->fld_attr->group_label,
                        'frm_uniq_id' => $this->frm_uniq_id
                    ]);
                }
                else {
                   return "";
                }
            }
            else
            {
                if (strlen($this->fld_attr->default_value) > 0 && $this->item_id == 0)
                {
                        $this->item_value = $this->fld_attr->default_value;
                }
            
                return view('fields.hidden', [
                    'frm_uniq_id' => $this->frm_uniq_id, 
                    'item_field' => $this->item_field, 
                    'item_value' => $this->item_value
                ]);
            }
        }
        
        private function get_visible_field_value_htm()
        { 
            switch ($this->fld_attr->type_sys_name) {
                case "rel_id":
                    return $this->get_htm_for_dropdown_field();
                case "autocompleate":
                    return $this->get_htm_for_autocompleate_field();
                case "tree":
                    return $this->get_htm_for_tree_field();
                case "date":
                    return $this->get_htm_for_date_field();
                case "datetime":
                    return $this->get_htm_for_datetime_field();
                case "bool":
                    return $this->get_htm_for_bool_field();
                case "text":
                    return $this->get_htm_for_textarea_field();
                case "html_text":
                    return $this->get_htm_for_richtext_field();
                case "soft_code":
                    return $this->get_htm_for_softcode_field();
                case "int":
                    return $this->get_htm_for_integer_field();
                case "decimal":
                    return $this->get_htm_for_decimal_field();
                case "file":
                    return $this->get_htm_for_file_field();
                case "reg_nr":
                    return $this->get_htm_for_regnr_field();
                case "password":
                    return $this->get_htm_for_password_field();
                case "color":
                    return $this->get_htm_for_color_field();
                default:
                    // For all the rest field types return simple text input field
                    return $this->get_htm_for_text_field();
            }
        }
        
        private function set_field_core_properties()
        {
            if ($this->parent_item_id > 0 && $this->fld_attr->field_id == $this->parent_field_id)
            {
                // Form is opened from sub-grid
                $this->item_value = $this->parent_item_id;
                $this->is_hidden_field = 1;
                
                $this->is_sub_grid_form = 1;
            }
            else
            {                
                $this->item_value = "";
                
                if ($this->data_row[$this->item_field] != null)
                {				
                        $this->item_value = $this->data_row[$this->item_field];

                }
                else
                {
                        if ($this->fld_attr->type_sys_name == "int" || $this->fld_attr->type_sys_name == "bool")
                        {
                                $this->item_value = 0;
                        }
                        else
                        {
                                $this->item_value = "";
                        }
                }

                $this->is_hidden_field = $this->fld_attr->is_hidden;
            }
        }  
        
        public static function getBindedFieldsItemsSQL($binded_field_id, $binded_rel_field_id, $binded_rel_field_value)
        {
            if ($binded_rel_field_value == null)
            {
                $binded_rel_field_value = 0;
            }
            
            $sql = "
            SELECT
                    lf_rel.db_name as rel_field_name,
                    o_rel.db_name as rel_table_name,
                    lf_rel_v.db_name as rel_value_name,
                    o_rel.is_multi_registers,
                    lf_rel.list_id as rel_list_id
            FROM
                    dx_lists_fields lf	
                    inner join dx_lists l_rel on lf.rel_list_id = l_rel.id
                    inner join dx_objects o_rel on l_rel.object_id = o_rel.id
                    inner join dx_lists_fields lf_rel on lf.rel_display_field_id = lf_rel.id
                    inner join dx_lists_fields lf_rel_v on lf_rel_v.id = :binded_rel_field_id
            WHERE
                    lf.id = :binded_field_id
            ";

            $fields = DB::select($sql, array('binded_rel_field_id' => $binded_rel_field_id, 'binded_field_id' => $binded_field_id));

            if (count($fields) == 0)
            {
                throw new Exceptions\DXWrongBindedFieldException($binded_field_id);
            }

            $row = $fields[0];
            $sql_multi = "";

            if ($row->is_multi_registers == 1)
            {
                    $sql_multi = " AND multi_list_id = " . $row->rel_list_id;
            }

            $sql_rel = "SELECT id, " . $row->rel_field_name . " as txt FROM " . $row->rel_table_name . " WHERE " . $row->rel_value_name . "=" . $binded_rel_field_value . $sql_multi . " ORDER BY " . $row->rel_field_name;

            return $sql_rel;
        }
        
        private function get_htm_for_dropdown_field()
        {            
            if (strlen($this->fld_attr->default_value) > 0 && $this->item_id == 0)
            {
                    $this->item_value = $this->fld_attr->default_value;
            }
                
            $items = $this->get_dropdown_items();
            
            if ($this->fld_attr->is_required && count($items) == 1) {
                $this->item_value = $items[0]->id;
            }
            
            return view('fields.dropdown', [
                    'frm_uniq_id' => $this->frm_uniq_id, 
                    'item_field' => $this->item_field, 
                    'item_value' => $this->item_value,
                    'is_disabled' => ($this->fld_attr->is_readonly) ? 1 : $this->is_disabled_mode,
                    'binded_field_name' => $this->fld_attr->binded_field_name,
                    'binded_field_id' => $this->fld_attr->binded_field_id,
                    'binded_rel_field_id' => $this->fld_attr->binded_rel_field_id,
                    'items' => $items,
                    'is_required' => $this->fld_attr->is_required
            ])->render();
        }
        
        private function get_htm_for_autocompleate_field()
        {            
            $txt_display = "";
            $items = $this->get_dropdown_items();
            
            if (count($items) > 0)
            {
                $txt_display = $items[0]->txt;
            }
            
            if (!($this->item_value > 0))
            {
                    $this->item_value = 0;
                    $txt_display = "";
            }
            
            $form_url = getListFormURL($this->fld_attr->rel_list_id);
            
            $frm_uniq_id_js = str_replace("-", "_", $this->frm_uniq_id);
            
            return view('fields.autocompleate', [
                    'frm_uniq_id' => $this->frm_uniq_id, 
                    'item_field' => $this->item_field, 
                    'item_value' => $this->item_value,
                    'is_disabled' => ($this->fld_attr->is_readonly) ? 1 : $this->is_disabled_mode,
                    'rel_list_id' => $this->fld_attr->rel_list_id,
                    'rel_field_id' => $this->fld_attr->rel_field_id,
                    'rel_view_id' => $this->fld_attr->rel_view_id,
                    'rel_display_formula_field' => $this->fld_attr->rel_display_formula_field,
                    'txt_display' => $txt_display,
                    'is_required' => $this->fld_attr->is_required,
                    'form_url' => $form_url,
                    'frm_uniq_id_js' => $frm_uniq_id_js
            ])->render();
        }
        
        private function set_classifier_default_value()
        {
            if ($this->item_id == 0)
            {
                // Default value
                if (strlen($this->fld_attr->default_value) > 0 )
                {
                    if ($this->fld_attr->default_value == "[ME]")
                    {
                            $this->item_value = Auth::user()->id;
                    }
                    else
                    {
                            $tmp_val = $this->fld_attr->default_value;

                            if (is_numeric($tmp_val))
                            {
                                    $this->item_value = (int) $tmp_val;
                            }
                    }
                }
            }
        }
        
        private function get_dropdown_items()
        {
            $this->set_classifier_default_value();                      
            $sql_rel = "";
            
            if ($this->binded_field_id == $this->fld_attr->field_id)
            {
                // This is binded field, binding was set previously
                // It is expected that binded fields follows each other (no other fields between)
                
                $sql_rel = $this->getBindedFieldsItemsSQL($this->binded_field_id, $this->binded_rel_field_id, $this->binded_rel_field_value);
            }
            else
            {
                $sql_item = "";
                if ($this->fld_attr->type_sys_name == "autocompleate" && $this->item_value > 0)
                {
                        $sql_item = " AND id = " . $this->item_value;
                }
                                
                $sql_rel = getLookupSQL($this->fld_attr->rel_list_id, $this->fld_attr->rel_table_name, $this->fld_attr->rel_field_name, "txt");
                
                $sql_rel .= $sql_item . " ORDER BY txt";
                
                if ($this->fld_attr->binded_field_id > 0)
                {
                    $this->binded_rel_field_value = $this->item_value;
                    $this->binded_field_id = $this->fld_attr->binded_field_id;
                    $this->binded_rel_field_id = $this->fld_attr->binded_rel_field_id;
                }
            }            
            
            try 
            {
                return DB::select($sql_rel);
            }
            catch(\Exception $e)
            {
                throw new Exceptions\DXWrongDropdownSQLException($sql_rel);
            }
        }
        
        private function get_htm_for_date_field()
        {
            return view('fields.datetime', [
                    'frm_uniq_id' => $this->frm_uniq_id, 
                    'item_field' => $this->item_field, 
                    'item_value' => $this->item_value,
                    'is_disabled' => ($this->fld_attr->is_readonly) ? 1 : $this->is_disabled_mode,
                    'fld_width' => '130',
                    'tm_format' => 'd.m.Y',
                    'is_time' => 'false',
                    'is_required' => $this->fld_attr->is_required
            ])->render();
        }
        
        private function get_htm_for_datetime_field()
        {
            return view('fields.datetime', [
                    'frm_uniq_id' => $this->frm_uniq_id, 
                    'item_field' => $this->item_field, 
                    'item_value' => $this->item_value,
                    'is_disabled' => ($this->fld_attr->is_readonly) ? 1 : $this->is_disabled_mode,
                    'fld_width' => '180',
                    'tm_format' => 'd.m.Y H:i',
                    'is_time' => 'true',
                    'is_required' => $this->fld_attr->is_required
            ])->render();
        }
        
        private function get_htm_for_bool_field()
        {
            $sel_yes = "checked='checked'";
            $sel_no = "checked='checked'";
            
            if (strlen($this->fld_attr->default_value) > 0 && $this->item_id == 0)
            {
                    $this->item_value = $this->fld_attr->default_value;
            }
            
            if (strlen($this->item_value) == 0)
            {    
                $this->item_value = 0;
            }
            
            if ($this->item_value == 1)
            {
                $sel_no= "";
            }
            else
            {
                $sel_yes = "";
            }
                                
            return view('fields.bool', [
                    'item_field' => $this->item_field, 
                    'is_disabled' => ($this->fld_attr->is_readonly) ? 1 : $this->is_disabled_mode,
                    'sel_yes' => $sel_yes,
                    'sel_no' => $sel_no
            ])->render();  
        }
        
        private function get_htm_for_textarea_field()
        {
            return view('fields.textarea', [
                    'frm_uniq_id' => $this->frm_uniq_id, 
                    'item_field' => $this->item_field, 
                    'item_value' => $this->item_value,
                    'is_disabled' => ($this->fld_attr->is_readonly) ? 1 : $this->is_disabled_mode,
                    'max_lenght' => $this->fld_attr->max_lenght
            ])->render(); 
        }
        
        private function get_htm_for_richtext_field()
        {
            return view('fields.textarea_html', [
                    'frm_uniq_id' => $this->frm_uniq_id, 
                    'item_field' => $this->item_field, 
                    'item_value' => $this->item_value,
                    'is_disabled' => ($this->fld_attr->is_readonly) ? 1 : $this->is_disabled_mode
            ])->render();             
        }
        
        private function get_htm_for_softcode_field()
        {
            return view('fields.textarea_code', [
                    'frm_uniq_id' => $this->frm_uniq_id, 
                    'item_field' => $this->item_field, 
                    'item_value' => $this->item_value,
                    'is_disabled' => ($this->fld_attr->is_readonly) ? 1 : $this->is_disabled_mode
            ])->render(); 
        }
        
        private function get_htm_for_integer_field()
        {
            if (strlen($this->fld_attr->default_value) > 0 && $this->item_id == 0)
            {
                if (is_numeric($this->fld_attr->default_value))
                {
                    $this->item_value = $this->fld_attr->default_value;
                }
                else
                {
                    $this->item_value = 0;
                }
            }
            
            return view('fields.int', [
                    'frm_uniq_id' => $this->frm_uniq_id, 
                    'item_field' => $this->item_field, 
                    'item_value' => $this->item_value,
                    'is_disabled' => ($this->fld_attr->is_readonly) ? 1 : $this->is_disabled_mode,
                    'is_required' => $this->fld_attr->is_required
            ])->render(); 
        }
        
        private function get_htm_for_decimal_field() 
        {
            if (strlen($this->fld_attr->default_value) > 0 && $this->item_id == 0)
            {
                if (is_numeric($this->fld_attr->default_value))
                {
                    $this->item_value = $this->fld_attr->default_value;
                }
                else
                {
                    $this->item_value = 0;
                }
            }
            
            return view('fields.decimal', [
                    'frm_uniq_id' => $this->frm_uniq_id, 
                    'item_field' => $this->item_field, 
                    'item_value' => str_replace(".", ",", $this->item_value),
                    'is_disabled' => ($this->fld_attr->is_readonly) ? 1 : $this->is_disabled_mode,
                    'is_required' => $this->fld_attr->is_required
            ])->render(); 
        }
        
        private function get_htm_for_file_field()
        {
            $class_exist = "new";
            if (strlen($this->item_value) > 0)
            {
                    $class_exist = "exists";
            }

            if ($this->item_id == 0 && $this->fld_attr->is_multiple_files)
            {
                return view('fields.file_multi', [
                        'item_field' => $this->item_field,
                        'is_required' => $this->fld_attr->is_required,
                        'frm_uniq_id' => $this->frm_uniq_id
                    ])->render();
            }
            else
            {
                if ($this->fld_attr->is_image_file == 1)
                {
                    return view('fields.image', [
                        'item_id' => $this->item_id, 
                        'list_id' => $this->list_id,
                        'field_id' => $this->fld_attr->field_id,
                        'item_field_remove' => str_replace('_name', '_removed', $this->item_field),
                        'file_guid' => $this->data_row[str_replace('_name', '_guid', $this->item_field)],
                        'item_field' => $this->item_field,
                        'item_value' => $this->item_value,
                        'is_disabled' => ($this->fld_attr->is_readonly) ? 1 : $this->is_disabled_mode,
                        'class_exist' => $class_exist,
                        'is_required' => $this->fld_attr->is_required
                    ])->render();
                }
                else
                {
                    return view('fields.file', [
                        'item_id' => $this->item_id, 
                        'list_id' => $this->list_id,
                        'field_id' => $this->fld_attr->field_id,
                        'item_field_remove' => str_replace('_name', '_removed', $this->item_field),
                        'item_field' => $this->item_field,
                        'item_value' => $this->item_value,
                        'is_disabled' => ($this->fld_attr->is_readonly) ? 1 : $this->is_disabled_mode,
                        'class_exist' => $class_exist,
                        'is_required' => $this->fld_attr->is_required
                    ])->render(); 
                }
            }
        }
        
        private function get_htm_for_regnr_field()
        {
            if (strlen($this->fld_attr->default_value) > 0 && $this->item_id == 0)
            {
                    $this->item_value = $this->fld_attr->default_value;
            }
                                
            return view('fields.regnr', [
                    'frm_uniq_id' => $this->frm_uniq_id, 
                    'item_field' => $this->item_field, 
                    'item_value' => $this->item_value,
                    'is_disabled' => ($this->fld_attr->is_readonly) ? 1 : $this->is_disabled_mode,
                    'max_lenght' => $this->fld_attr->max_lenght,
                    'is_required' => $this->fld_attr->is_required
            ])->render(); 
        }
        
        /**
        * Atgriez krāsas ievades lauka HTML
        *
        * @return string  Krāsas ievades lauka HTML 
        */
        
        private function get_htm_for_color_field()
        {
            if (strlen($this->fld_attr->default_value) > 0 && $this->item_id == 0)
            {
                    $this->item_value = $this->fld_attr->default_value;
            }
                                
            return view('fields.color', [
                    'frm_uniq_id' => $this->frm_uniq_id, 
                    'item_field' => $this->item_field, 
                    'item_value' => $this->item_value,
                    'is_disabled' => ($this->fld_attr->is_readonly) ? 1 : $this->is_disabled_mode,
                    'max_lenght' => $this->fld_attr->max_lenght,
                    'is_required' => $this->fld_attr->is_required
            ])->render(); 
        }
        
        private function get_htm_for_text_field()
        {
            if (strlen($this->fld_attr->default_value) > 0 && $this->item_id == 0)
            {
                    $this->item_value = $this->fld_attr->default_value;
            }
                                
            return view('fields.text', [
                    'frm_uniq_id' => $this->frm_uniq_id, 
                    'item_field' => $this->item_field, 
                    'item_value' => $this->item_value,
                    'is_disabled' => ($this->fld_attr->is_readonly) ? 1 : $this->is_disabled_mode,
                    'max_lenght' => $this->fld_attr->max_lenght,
                    'is_required' => $this->fld_attr->is_required
            ])->render(); 
        }
        
         /**
         * Atgriež HTML paroles laukam
         * 
         * @return string paroles lauka HTML
         */
        private function get_htm_for_password_field()
        {
            if (strlen($this->fld_attr->default_value) > 0 && $this->item_id == 0)
            {
                    $this->item_value = $this->fld_attr->default_value;
            }
                                
            return view('fields.password', [
                    'frm_uniq_id' => $this->frm_uniq_id, 
                    'item_field' => $this->item_field, 
                    'item_value' => $this->item_value,
                    'is_disabled' => ($this->fld_attr->is_readonly) ? 1 : $this->is_disabled_mode,
                    'max_lenght' => $this->fld_attr->max_lenght,
                    'is_required' => $this->fld_attr->is_required
            ])->render(); 
        }
        
        private function get_htm_for_tree_field()
        {
            $is_fld_disabled = ($this->fld_attr->is_readonly) ? 1 : $this->is_disabled_mode;
            
            if (strlen($this->fld_attr->default_value) > 0 && $this->item_id == 0)
            {
                    $this->item_value = $this->fld_attr->default_value;
            }
            
            $is_shown_param = str_replace("-", "_", $this->frm_uniq_id);
            
            $sql_multi = "";
            if ($this->fld_attr->is_multi_registers==1)
            {
                    $sql_multi = " AND multi_list_id = " . $this->fld_attr->rel_list_id;
            }
                
            $sql_rel = "SELECT id, " . $this->fld_attr->rel_parent_field_name . " as parent_id, " . $this->fld_attr->rel_field_name . " as title FROM " . $this->fld_attr->rel_table_name . " WHERE 1=1" . $sql_multi . " ORDER BY " . $this->fld_attr->rel_field_name;
                        
            DB::setFetchMode(PDO::FETCH_ASSOC); // We need to get values as array to use it in recursion                  
                    
            $rows = DB::select($sql_rel);                 
                    
            DB::setFetchMode(PDO::FETCH_CLASS); // Set back default fetch mode
            
            $tree = $this->generatePageTree($rows);
            
            return view('fields.tree', [
                    'frm_uniq_id' => $this->frm_uniq_id, 
                    'item_field' => $this->item_field, 
                    'item_value' => $this->item_value,
                    'item_full_path' => $this->tree_full_path,
                    'is_disabled' => $is_fld_disabled,
                    'form_title' => 'Saistītā ieraksta izvēle',
                    'is_shown_param' => $is_shown_param,
                    'tree' => ($is_fld_disabled) ? '' : $tree
            ])->render(); 
        }
        
        private function generatePageTree($datas, $parent = 0, $depth=0, $full_path = ""){
            if($depth > 1000) return ''; // Make sure not to have an endless recursion
            
            $tree = '<ul>';
                        
            if (strlen($full_path) > 0)
            {
                $full_path .= "->";
            }
            
            for($i=0, $ni=count($datas); $i < $ni; $i++){
                if($datas[$i]['parent_id'] == $parent){                    
                    
                    $node_path = $full_path . $datas[$i]['title'];
                    
                    if ($this->item_value == $datas[$i]['id'])
                    {
                        $this->tree_full_path = $node_path;
                    }
                    
                    $tree .= view('elements.tree_node', [
                        'node_path' => $node_path, 
                        'node_id' => $datas[$i]['id'], 
                        'node_title' => $datas[$i]['title'],
                        'node_children' => $this->generatePageTree($datas, $datas[$i]['id'], $depth+1, $node_path)
                    ])->render();                    
                }
            }
            $tree .= '</ul>';
            return $tree;
        } 
    }
}
    