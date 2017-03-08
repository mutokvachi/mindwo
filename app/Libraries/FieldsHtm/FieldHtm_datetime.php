<?php

namespace App\Libraries\FieldsHtm
{

    use Config;

    /**
     * Datuma/laika lauka attēlošanas klase
     */
    class FieldHtm_datetime extends FieldHtm
    {

        /**
         * Atgriež lauka attēlošanas HTML
         */
        public function getHtm()
        {
            return view('fields.datetime', [
                        'frm_uniq_id' => $this->frm_uniq_id,
                        'item_field' => $this->fld_attr->db_name,
                        'item_value' => $this->item_value,
                        'is_disabled' => ($this->fld_attr->is_readonly) ? 1 : $this->is_disabled_mode,
                        'fld_width' => '180',
                        'tm_format' => Config::get('dx.txt_datetime_format', 'd.m.Y H:i'),
                        'is_time' => 'true',
                        'is_required' => $this->fld_attr->is_required
                    ])->render();
        }

        /**
         * Returns textual value of the field
         */
        public function getTxtVal()
        {
            return ($this->item_value) ? long_date($this->item_value) : "";
        }

         /**
         * Uzstāda noklusēto vērtību jauna ieraksta gadījumā
         */
        protected function setDefaultVal()
        {
            if ($this->item_id > 0) {
                return;
            }

            $this->item_value = $this->generateDate();
        }

        /**
         * Generated today's date
         * @return Date Today's date
         */
        private function generateDate()
        {
            if (strlen($this->fld_attr->default_value) == 0) {
                return null;
            }

            if ($this->fld_attr->default_value == "[NOW]") {
                return date(Config::get('dx.txt_datetime_format', 'd.m.Y H:i'));
            }

            return null;
        }

    }

}