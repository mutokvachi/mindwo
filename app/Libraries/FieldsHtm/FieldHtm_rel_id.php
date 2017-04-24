<?php

namespace App\Libraries\FieldsHtm
{

    use Auth;
    use App\Exceptions;
    use DB;
    use Log;
    
    /**
     * Izkrītošās izvēlnes lauka attēlošanas klase
     */
    class FieldHtm_rel_id extends FieldHtm
    {

        /**
         * Ja formā ir 2 savstarpēji saistītās izkrītošās izvēlnes, tad šajā parametrā norāda vērtību, pēc kuras tiks atlasīta otrā izvēlne
         * @var integer 
         */
        public $binded_rel_field_value = 0;

        /**
         * Ja formā ir 2 savstarpēji saistītās izkrītošās izvēlnes, tad šajā parametrā norāda pirmās izvēlnes lauka ID
         * @var integer 
         */
        public $binded_field_id = 0;

        /**
         * Ja formā ir 2 savstarpēji saistītās izkrītošās izvēlnes, tad šajā parametrā norāda otrās izvēlnes lauka ID
         * @var integer 
         */
        public $binded_rel_field_id = 0;

        /**
         * Atgriež lauka attēlošanas HTML
         */
        public function getHtm()
        {
            $items = $this->get_dropdown_items();

            if ($this->fld_attr->is_required && count($items) == 1) {
                $this->item_value = $items[0]->id;
            }

            $form_url = getListFormURL($this->fld_attr->rel_list_id);
            $frm_uniq_id_js = str_replace("-", "_", $this->frm_uniq_id);

            return view('fields.dropdown', [
                        'frm_uniq_id' => $this->frm_uniq_id,
                        'item_field' => $this->fld_attr->db_name,
                        'item_value' => $this->item_value,
                        'is_disabled' => ($this->fld_attr->is_readonly) ? 1 : $this->is_disabled_mode,
                        'binded_field_name' => $this->fld_attr->binded_field_name,
                        'binded_field_id' => $this->fld_attr->binded_field_id,
                        'binded_rel_field_id' => $this->fld_attr->binded_rel_field_id,
                        'items' => $items,
                        'is_required' => $this->fld_attr->is_required,
                        'form_url' => $form_url,
                        'rel_list_id' => $this->fld_attr->rel_list_id,
                        'rel_field_id' => $this->fld_attr->rel_field_id,
                        'frm_uniq_id_js' => $frm_uniq_id_js
                    ])->render();
        }

        /**
         * Returns textual value of the field
         */
        public function getTxtVal()
        {
            if (!$this->item_value) {
                return "";
            }
            
            $items = $this->get_dropdown_items();
            foreach($items as $item) {
                if ($item->id == $this->item_value) {
                    return $item->txt;
                }
            }
            
            return "";
        }

        /**
         * Uzstāda noklusēto vērtību jauna ieraksta gadījumā
         */
        protected function setDefaultVal()
        {
            if ($this->item_id != 0 || strlen($this->fld_attr->default_value) == 0) {
                return;
            }

            if ($this->fld_attr->default_value == "[ME]") {
                $this->item_value = Auth::user()->id;
            }
            else {
                $tmp_val = $this->fld_attr->default_value;

                if (is_numeric($tmp_val)) {
                    $this->item_value = (int) $tmp_val;
                }
            }
        }

        /**
         * Atgriež izkrītošās izvēlnes vērtības
         * 
         * @return Array Masīvs ar vērtībām
         * @throws Exceptions\DXCustomException
         */
        private function get_dropdown_items()
        {
            try {
                $sql_rel = "";

                if ($this->binded_field_id == $this->fld_attr->field_id) {
                    // This is binded field, binding was set previously
                    // It is expected that binded fields follows each other (no other fields between)

                    return getBindedFieldsItems($this->binded_field_id, $this->binded_rel_field_id, $this->binded_rel_field_value);
                }
                else {
                    $sql_rel = getLookupSQL($this->fld_attr->rel_list_id, $this->fld_attr->rel_table_name, $this->fld_attr, "txt") . " ORDER BY txt";

                    if ($this->fld_attr->binded_field_id > 0) {
                        $this->binded_rel_field_value = $this->item_value;
                        $this->binded_field_id = $this->fld_attr->binded_field_id;
                        $this->binded_rel_field_id = $this->fld_attr->binded_rel_field_id;
                    }
                }


                return DB::select($sql_rel);
            }
            catch (\Exception $e) {
                Log::info("REL ID ERROR: " . $e->getMessage());
                throw new Exceptions\DXCustomException("Reģistra ar ID " . $this->fld_attr->list_id . " izkrītošās izvēlnes laukam '" . $this->fld_attr->db_name . "' nav iespējams izveidot korektu datu atlases pieprasījumu.");
            }
        }

    }

}