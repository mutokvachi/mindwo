<?php

namespace App\Libraries\FieldsHtm
{

    /**
     * Formas attēlošanas lauka izveidošanas klase
     */
    class FieldHtmFactory
    {

        /**
         * Izveido formas lauka attēlošanas klasi
         * 
         * @param Array     $fld_attr           Masīvs ar lauka atribūtiem
         * @param integer   $item_id            Ieraksta ID (jauniem ierakstiem tas ir 0)
         * @param mixed     $item_value         Ieraksta vērtība (ID, teksts, datums utt)
         * @param integer   $list_id            Ieraksta reģistra ID
         * @param string    $frm_uniq_id        Formas HTML identifikators
         * @param string    $is_disabled_mode   Vai forma ir atvērta nerediģējama
         * @param boolean   $is_item_editable   Vai lietotājam ir rediģēšanas tiesības uz ierakstu
         * @return \App\Libraries\FieldsHtm\class
         */
        public static function build_field($fld_attr, $item_id, $item_value, $list_id, $frm_uniq_id, $is_disabled_mode, $is_item_editable)
        {
            $class = "App\\Libraries\\FieldsHtm\\FieldHtm_" . $fld_attr->type_sys_name;

            if (!class_exists($class)) {
                $class = "App\\Libraries\\FieldsHtm\\FieldHtm_varchar";
            }

            return new $class($fld_attr, $item_id, $item_value, $list_id, $frm_uniq_id, $is_disabled_mode, $is_item_editable);
        }

    }

}