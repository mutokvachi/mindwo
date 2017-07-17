<?php

/* 
 * Globālās palīgfunkcijas CMS formu funkcionalitātei
 */

use mindwo\pages\Exceptions\PagesException;

/**
 * Returns default view row for lookup SQL preparation
 * 
 * @param integer $list_id Register ID
 * @return array Row from table dx_views
 */
function getLookupViewRow($list_id) {
    return  DB::table('dx_views')
            ->where('list_id', '=', $list_id)                
            ->orderBy('is_for_lookup', 'DESC')
            ->orderBy('is_default', 'DESC')
            ->orderBy('is_hidden_from_main_grid')
            ->first();
}

/**
 * Izgūst uzmeklēšanas lauka ierakstu atlasīšanas SQL izteiksmi
 * 
 * @param integer $list_id Reģistra ID
 * @param string $table_name Tabulas nosaukums
 * @param string $field_item Lauks (tabulas dx_lists_fields ieraksts)
 * @param string $txt_alias Lauka alternatīvais nosaukums
 * @return string SQL izteiksme uzmeklēšanas lauka vērtību atlasei
 * 
 * @throws Exceptions\DXCustomException
 */
function getLookupSQL($list_id, $table_name, $field_item, $txt_alias) {
    
    $view_row = getLookupViewRow($list_id);
    
    $txt_field_name = $field_item->rel_field_name;           
    $view_obj = new \App\Libraries\View($list_id, $view_row->id, Auth::user()->id);
    
    $view_obj->is_rights_check_off = ($field_item->is_right_check) ? 0 : 1; // atslēdzam tiesību pārbaudi, ja tā norādīts lauka iestatījumos
    
    $sql = $view_obj->get_view_sql();

    $sql_id = $table_name . ".id as id";
    $sql_txt = $table_name . "." . $txt_field_name . " as " . $table_name . "_" . $txt_field_name;
    
    if (!str_contains($sql, $sql_txt)) {
        throw new PagesException(sprintf(trans('mindwo/pages::errors.lookup_view_error'), $table_name, $txt_field_name));
    }

    if (!str_contains($sql, $sql_id)) {
        $sql_txt = $sql_id . ", " . $table_name . "." . $txt_field_name . " as txt";
    }
    else
    {
        $sql_txt = $table_name . "." . $txt_field_name . " as " . $txt_alias;
    }

    $sql = str_replace($table_name . "." . $txt_field_name . " as " . $table_name . "_" . $txt_field_name, $sql_txt, $sql);
    
    return $sql;
}

/**
 * Atgriež datus saistīto izkrītošo izvēlņu otrajai izvēlnei
 * 
 * @param integer $binded_field_id Pirmās izvēlnes saistītā lauka ID
 * @param integer $binded_rel_field_id Otrās izvēlnes saistītā lauka ID
 * @param integer $binded_rel_field_value Lauka vērtība pirmajā izvēlnē (atkarībā no tās ielādēs otro izvēlni)
 * @return Array  Masīvs ar izkrītošās izvēlnes ierakstiem
 * @throws Exceptions\DXWrongBindedFieldException
 */
function getBindedFieldsItems($binded_field_id, $binded_rel_field_id, $binded_rel_field_value)
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
        throw new PagesException("Saistītajai izvēlnei (lauka ID: " . $binded_field_id . ") nav iespējams izveidot korektu SQL izteiksmi.");
    }

    $row = $fields[0];
    
    $tb_rel = DB::table($row->rel_table_name)
               ->select('id', $row->rel_field_name . ' as txt')
               ->where($row->rel_value_name, '=', $binded_rel_field_value);
    

    if ($row->is_multi_registers == 1)
    {
        $tb_rel->where('multi_list_id', '=', $row->rel_list_id);
    }
    
    $tb_rel->orderBy($row->rel_field_name);
    
    return $tb_rel->get();
}