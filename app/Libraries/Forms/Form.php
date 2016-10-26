<?php

namespace App\Libraries\Forms;

use App\Exceptions;

class Form
{
	protected $listId, $params;
	
	public function __construct($listId)
	{
		$this->listId = $listId;
		$this->params = $this->getFormParams();
	}
	
	public function render()
	{
		
	}
	
	protected function getFormFieldsHTML($frm_uniq_id, $list_id, $item_id, $parent_item_id, $parent_field_id, $params)
	{
		$row_data = null;
		
		if($item_id > 0)
		{
			$row_data = $this->getFormItemDataRow($list_id, $item_id, $params);
		}
		
		$fields = $this->getFormFields($params);
		
		$fields_htm = "";
		
		$binded_field_id = 0;
		$binded_rel_field_id = 0;
		$binded_rel_field_value = 0;
		
		foreach($fields as $row)
		{
			if($row->db_name == "id" && $item_id == 0)
			{
				// skip ID field for new item form
				continue;
			}
			
			$fld_obj = new FormField($row, $list_id, $item_id, $parent_item_id, $parent_field_id, $row_data,
				$frm_uniq_id);
			$fld_obj->is_disabled_mode = $this->is_disabled;
			
			$fld_obj->binded_field_id = $binded_field_id;
			$fld_obj->binded_rel_field_id = $binded_rel_field_id;
			$fld_obj->binded_rel_field_value = $binded_rel_field_value;
			
			$fld_obj->is_editable_wf = $this->is_editable_wf;
			
			if($row->tab_id)
			{
				if(!isset($this->arr_data_tabs[$row->tab_id]))
				{
					$this->arr_data_tabs[$row->tab_id] = "";
				}
				$this->arr_data_tabs[$row->tab_id] .= $fld_obj->get_field_htm();
			}
			else
			{
				$fields_htm .= $fld_obj->get_field_htm();
			}
			
			$binded_field_id = $fld_obj->binded_field_id;
			$binded_rel_field_id = $fld_obj->binded_rel_field_id;
			$binded_rel_field_value = $fld_obj->binded_rel_field_value;
		}
		
		return $fields_htm;
	}
	
	protected function getFormItemDataRow($list_id, $item_id, $params)
	{
		$fields_rows = $this->getFormSQLFields($list_id);
		
		if(count($fields_rows) == 0)
		{
			throw new Exceptions\DXCustomException("Reģistrs ar ID " . $list_id . " nav atrasts!");
		}
		
		DB::setFetchMode(PDO::FETCH_ASSOC); // We need to get values dynamicly
		
		$rows = $this->getFormItemRows($fields_rows, $params, $item_id);
		
		DB::setFetchMode(PDO::FETCH_CLASS); // Set back default fetch mode
		
		if(count($rows) == 0)
		{
			throw new Exceptions\DXCustomException("Reģistra ar ID " . $list_id . " ieraksts ar ID " . $item_id . " nav atrasts!");
		}
		
		return $rows[0];
	}
	
	protected function getFormFields()
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
				lf_rel.id as rel_field_id,
				o_rel.db_name as rel_table_name,
				lf_par.db_name as rel_parent_field_name,
				lf_par.id as rel_parent_field_id,
				o_rel.is_multi_registers,
				lf_bind.id as binded_field_id,
				lf_bind.db_name as binded_field_name,
				lf_bindr.id as binded_rel_field_id,
				lf_bindr.db_name as binded_rel_field_name,
				lf.default_value,
				ft.height_px,
				ifnull(lf.rel_view_id,0) as rel_view_id,
				ifnull(lf.rel_display_formula_field,'') as rel_display_formula_field,
				lf.is_image_file,
				lf.is_multiple_files,
				lf.hint,
				lf.is_manual_reg_nr,
				lf.reg_role_id,
				ff.tab_id,
				ff.group_label
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
				left join dx_lists_fields lf_par on lf.rel_parent_field_id = lf_par.id
				left join dx_lists_fields lf_bind on lf.binded_field_id = lf_bind.id
				left join dx_lists_fields lf_bindr on lf.binded_rel_field_id = lf_bindr.id
			WHERE
				ff.form_id = :form_id
			ORDER BY
				ff.order_index
			";
		
		$fields = DB::select($sql, array('form_id' => $this->params->form_id));
		
		if(count($fields) == 0)
		{
			throw new Exceptions\DXCustomException("Forma ar ID " . $this->params->form_id . " nav atrasta!");
		}
		
		return $fields;
	}
	
	protected function getFormParams()
	{
		$sql = "
			SELECT
				o.db_name as list_obj_db_name,
				f.id as form_id,
				f.title as form_title,
				f.zones_count,
				o.is_multi_registers,
				f.width,
				f.is_vertical_tabs
			FROM
				dx_lists l
				inner join dx_objects o on l.object_id = o.id
				inner join dx_forms f on f.list_id = l.id
			WHERE
				l.id = :list_id
			LIMIT 0,1
			";
		
		$list_rows = DB::select($sql, array("list_id" => $this->listId));
		
		if(count($list_rows) == 0)
		{
			throw new Exceptions\DXListNotFoundException($this->listId);
		}
		
		return $list_rows[0];
	}
	
	protected function getFormSQLFields()
	{
		$sql = "
			SELECT
				lf.db_name,
				lf.title_list,
				ft.is_date,
				ft.is_integer,
				ft.is_decimal,
				o.db_name as rel_table_db_name,
				rf.db_name as rel_field_db_name,
				ft.sys_name	
			FROM
				dx_lists_fields lf
				inner join dx_field_types ft on lf.type_id = ft.id
				left join dx_lists_fields rf on lf.rel_display_field_id = rf.id
				left join dx_lists rl on rl.id = lf.rel_list_id
				left join dx_objects o on rl.object_id = o.id
			WHERE
				lf.list_id = :list_id and (lf.formula is null or lf.rel_list_id is not null)
			";
		
		return DB::select($sql, array("list_id" => $this->listId));
	}
}