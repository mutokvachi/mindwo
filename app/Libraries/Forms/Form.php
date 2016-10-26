<?php

namespace App\Libraries\Forms;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Exceptions;
use App\Libraries\Rights;
use App\Libraries\FormField;
use App\Libraries\Workflows;
use Webpatser\Uuid\Uuid;
use PDO;

class Form
{
	public $tabList = [];
	public $disabled = false;
	public $editMode = false;
	protected $listId;
	protected $itemId;
	protected $formUid;
	protected $tabUid;
	protected $params;
	protected $formFields;
	protected $itemData;
	protected $editable;
	protected $canEdit;
	protected $canDelete;
	protected $tabsData;
	
	public function __construct($listId, $itemId = null)
	{
		$this->listId = $listId;
		$this->itemId = $itemId;
		$this->formUid = Uuid::generate(4);
		$this->tabUid = Uuid::generate(4);
		$this->params = $this->getFormParams();
		$this->formFields = $this->getFormFields();
		$this->editable = Rights::getIsEditRightsOnItem($this->listId, $this->itemId);
		
		if($this->itemId)
		{
			$this->itemData = $this->getFormItemDataRow();
		}
	}
	
	static public function create($listId, $itemId)
	{
		return new self($listId, $itemId);
	}
	
	public function render()
	{
		$result = view('elements.form', [
			'frm_uniq_id' => $this->formUid,
			'form_title' => $this->params->form_title,
			'fields_htm' => $this->renderFields(),
			'tab_id' => $this->tabUid,
			'tabs_htm' => $this->renderTabs(),
			'form_id' => $this->params->form_id,
			'grid_htm_id' => '',
			'list_id' => $this->listId,
			'item_id' => $this->itemId,
			'parent_field_id' => 0,
			'parent_item_id' => 0,
			'is_multi_registers' => $this->params->is_multi_registers,
			'js_code' => DB::table('dx_forms_js')->where('form_id', '=', $this->params->form_id)->get(),
			
			// Formai norādītie JavaScript
			'js_form_id' => str_replace("-", "_", $this->formUid), // Formas GUID bez svītriņām, izmantojams JavaScript funkcijās kā mainīgais
			
			// if form is related to lookup or dropdown field from parent form
			'call_field_htm_id' => '',
			'call_field_type' => '',
			'call_field_id' => 0,
			
			'parent_form_htm_id' => '',
			
			'form_badge' => '',
			'is_form_reloaded' => 1,
			'form_width' => $this->params->width,
			
			// Pogu pieejamība un rediģēšanas režīms
			'form_is_edit_mode' => $this->editMode,
			'is_disabled' => $this->disabled,
			'is_edit_rights' => $this->canEdit,
			'is_delete_rights' => $this->canDelete,
			'is_info_tasks_rights' => false, // ($table_name == "dx_doc"),
			'workflow_btn' => 0, // $this->isWorkflowInit($this->listId, $this->itemId), // Uzstāda pazīmi, vai redzama darbplūsmu poga
			'is_custom_approve' => 0, // ($this->workflow && $this->workflow->is_custom_approve) ? 1 : 0,
			'is_editable_wf' => $this->editable,
			'is_word_generation_btn' => 0, // $this->getWordGenerBtn($list_id),
			'info_tasks' => [],
		])->render();
		
		return $result;
	}
	
	protected function renderFields()
	{
		$result = '';
		
		foreach($this->formFields as $row)
		{
			// skip ID field for new item form
			if($row->db_name == "id" && !$this->itemId)
			{
				continue;
			}
			
			$field = new FormField($row, $this->listId, $this->itemId, 0, 0, $this->itemData, $this->formUid);
			$field->is_disabled_mode = $this->disabled;
			$field->is_editable_wf = $this->editable;
			
			if($row->tab_id)
			{
				if(!isset($this->tabsData[$row->tab_id]))
				{
					$this->tabsData[$row->tab_id] = '';
				}
				
				$this->tabsData[$row->tab_id] .= $field->get_field_htm();
			}
			else
			{
				$result .= $field->get_field_htm();
			}
		}
		
		return $result;
	}
	
	protected function renderTabs()
	{
		$result = "";
		
		$tabs = $this->getFormTabs();
		
		if(count($tabs))
		{
			foreach($tabs as $tab)
			{
				$tab->data_htm = "";
				if($tab->is_custom_data && isset($this->tabsData[$tab->id]))
				{
					$tab->data_htm = $this->tabsData[$tab->id];
				}
			}
			
			$view_type = ($this->params->is_vertical_tabs) ? "elements.tabs_vert" : "elements.tabs";
			
			$result = view($view_type, [
				'tab_id' => $this->tabUid,
				'tabs_items' => $tabs,
				'frm_uniq_id' => $this->formUid,
				'item_id' => $this->itemId
			])->render();
		}
		
		return $result;
	}
	
	protected function getFormItemDataRow()
	{
		$fields_rows = $this->getFormSQLFields();
		
		if(count($fields_rows) == 0)
		{
			throw new Exceptions\DXCustomException("Reģistrs ar ID " . $this->listId . " nav atrasts!");
		}
		
		DB::setFetchMode(PDO::FETCH_ASSOC); // We need to get values dynamicly
		
		$rows = $this->getFormItemRows($fields_rows);
		
		DB::setFetchMode(PDO::FETCH_CLASS); // Set back default fetch mode
		
		if(count($rows) == 0)
		{
			throw new Exceptions\DXCustomException("Reģistra ar ID " . $this->listId . " ieraksts ar ID " . $this->itemId . " nav atrasts!");
		}
		
		return $rows[0];
	}
	
	protected function getFormItemRows($fields_rows)
	{
		$arr_flds = array();
		
		foreach($fields_rows as $row)
		{
			if($row->sys_name == "datetime")
			{
				array_push($arr_flds,
					DB::raw("DATE_FORMAT(" . $row->db_name . ",'%d.%m.%Y %H:%i') as " . $row->db_name));
			}
			else
			{
				if($row->sys_name == "date")
				{
					array_push($arr_flds, DB::raw("DATE_FORMAT(" . $row->db_name . ",'%d.%m.%Y') as " . $row->db_name));
				}
				else
				{
					if($row->sys_name == "file")
					{
						array_push($arr_flds, $row->db_name);
						array_push($arr_flds, str_replace("_name", "_guid", $row->db_name));
					}
					else
					{
						array_push($arr_flds, $row->db_name);
					}
				}
			}
		}
		
		return DB::table($this->params->list_obj_db_name)
			->select($arr_flds)
			->where('id', '=', $this->itemId)
			->get();
	}
	
	protected function setFormsRightsMode()
	{
		$right = Rights::getRightsOnList($this->listId);
		
		if($right == null)
		{
			if($this->itemId == 0 || !Workflows\Helper::isRelatedTask($this->listId, $this->itemId))
			{
				throw new Exceptions\DXCustomException("Jums nav nepieciešamo tiesību šajā reģistrā!");
			}
			
			// var vismaz skatīties ieraksta kartiņu
			if($this->isRelatedEditableTask($list_id, $item_id))
			{
				$this->disabled = 0; // var rediģēt ierakstu
			}
		}
		else
		{
			if($this->itemId == 0 && $right->is_new_rights == 0)
			{
				throw new Exceptions\DXCustomException("Jums nav nepieciešamo tiesību veidot jaunu ierakstu šajā reģistrā!");
			}
			
			$this->canDelete = $right->is_delete_rights;
			$this->canEdit = $right->is_edit_rights;
			
			if($right->is_edit_rights)
			{
				$this->disabled = 0; // var rediģēt, pēc noklusēšanas ir ka nevar
			}
		}
		
		$this->setFormEditMode($item_id);
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
	
	protected function getFormTabs()
	{
		$sql = "
			SELECT 
				* 
			FROM 
				dx_forms_tabs 
			WHERE 
				form_id = :form_id 
				AND (
						grid_list_id is null 
					OR  grid_list_id in 
						(
						select distinct 
							rl.list_id 
						from 
							dx_users_roles ur 
							inner join dx_roles_lists rl on ur.role_id = rl.role_id 
						where 
							ur.user_id = :user_id
						)
					) 
			ORDER BY 
				order_index
			";
		
		return DB::select($sql, array("form_id" => $this->params->form_id, 'user_id' => Auth::user()->id));
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