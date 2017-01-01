<?php

namespace App\Libraries\FieldsHtm
{

    /**
     * HTML redaktora lauka attēlošanas klase
     */
    class FieldHtm_html_text extends FieldHtm
    {

        /**
         * Atgriež lauka attēlošanas HTML
         */
        public function getHtm()
        {
            return view('fields.textarea_html', [
                        'frm_uniq_id' => $this->frm_uniq_id,
                        'item_field' => $this->fld_attr->db_name,
                        'item_value' => $this->fixLtRtTags($this->item_value),
                        'is_disabled' => ($this->fld_attr->is_readonly) ? 1 : $this->is_disabled_mode
                    ])->render();
        }

        /**
         * Returns textual value of the field
         */
        public function getTxtVal()
        {
            return $this->fixLtRtTags($this->item_value);
        }

        /**
         * Uzstāda noklusēto vērtību jauna ieraksta gadījumā
         */
        protected function setDefaultVal()
        {
            if ($this->item_id == 0 && strlen($this->fld_attr->default_value) > 0) {
                $this->item_value = $this->fld_attr->default_value;
            }
        }

        /**
         * Fix issue for TinyMCE when $lt; and &gt; are trated as HTML tags
         * @param string $val Text to be fixed
         * @return string Fixed text
         */
        private function fixLtRtTags($val)
        {

            $val = $this->mb_str_replace("&lt;", "&amp;lt;", $val);
            $val = $this->mb_str_replace("&gt;", "&amp;gt;", $val);
            return $val;
        }

        private function mb_str_replace($search, $replace, $subject)
        {
            if (is_array($subject)) {
                $ret = array();
                foreach ($subject as $key => $val) {
                    $ret[$key] = $this->mb_str_replace($search, $replace, $val);
                }
                return $ret;
            }

            foreach ((array) $search as $key => $s) {
                if ($s == '' && $s !== 0) {
                    continue;
                }
                $r = !is_array($replace) ? $replace : (array_key_exists($key, $replace) ? $replace[$key] : '');
                $pos = mb_strpos($subject, $s, 0, 'UTF-8');
                while ($pos !== false) {
                    $subject = mb_substr($subject, 0, $pos, 'UTF-8') . $r . mb_substr($subject, $pos + mb_strlen($s, 'UTF-8'), 65535, 'UTF-8');
                    $pos = mb_strpos($subject, $s, $pos + mb_strlen($r, 'UTF-8'), 'UTF-8');
                }
            }
            return $subject;
        }

    }

}