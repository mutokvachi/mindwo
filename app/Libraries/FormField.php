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
                        'frm_uniq_id' => $this->frm_uniq_id,
                        'fld_row_code' => $this->fld_attr->row_type_code
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
    }
}
    