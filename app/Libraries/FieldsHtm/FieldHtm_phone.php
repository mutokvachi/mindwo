<?php

namespace App\Libraries\FieldsHtm
{
    use DB;
    
    /**
     * Phone field - display dropdown with countries codes and number input
     * Phones are stored in data base 1 varchar field. It is stored whole number with country code in brackets and with +
     */
    class FieldHtm_phone extends FieldHtm
    {

        /**
         * Returns field's HTML
         */
        public function getHtm()
        {
            $code_part = $this->getCodePart();
            
            $country_list = \App\Libraries\DBHelper::getListByTable("dx_countries");
            
            $country_list_id = 0;
            if ($country_list) {
                $country_list_id = $country_list->id;
                $rights = \App\Libraries\Rights::getRightsOnList($country_list->id);
            }
            
            $is_new_rights = 0;
            
            if ($rights) {
                $is_new_rights = $rights->is_new_rights;
            }
            
            return view('fields.phone', [
                        'frm_uniq_id' => $this->frm_uniq_id,
                        'item_field' => $this->fld_attr->db_name,
                        'item_value' => $this->item_value,
                        'is_disabled' => ($this->fld_attr->is_readonly) ? 1 : $this->is_disabled_mode,
                        'max_lenght' => $this->fld_attr->max_lenght,
                        'is_required' => $this->fld_attr->is_required,
                        'countries' => DB::table('dx_countries')->whereNotNull('phone_code')->orderBy('code')->get(),
                        'code_part' => str_replace(")", "", str_replace("(", "", $code_part)),
                        'nr_part' => trim(str_replace($code_part, "", $this->item_value)),
                        'is_new_rights' => $is_new_rights,
                        'country_list_id' => $country_list_id
            ])->render();
        }
        
        /**
         * Returns textual value of the field
         */
        public function getTxtVal()
        {
            return $this->item_value;
        }
        
        /**
         * Returns phone number country code part
         * From (+371) 29131987 will be returned (+371)
         * 
         * @return string Phone number code with brackets
         */
        private function getCodePart() {
            $rez = "";
            
            if (strlen($this->item_value) == 0) {
                return "";
            }
            
            preg_match_all('/\((.*?)\)/', $this->item_value, $out);
            
            if (count($out) > 0 && isset($out[0]) && isset($out[0][0])) {
                $rez = $out[0][0];
            }
            
            return $rez;
        }

        /**
         * Set default value in case of new item
         */
        protected function setDefaultVal()
        {
            if ($this->item_id == 0 && strlen($this->fld_attr->default_value) > 0) {
                $this->item_value = $this->fld_attr->default_value;
            }
        }

    }

}