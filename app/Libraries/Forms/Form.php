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
use Log;

class Form
{
	public $tabList = [];
	public $skipFields = [];
	public $disabled = false;
	public $editMode = false;
	public $params;
	protected $listId;
	protected $itemId;
	protected $formUid;
	protected $tabUid;
	protected $formFields;
	protected $itemData;
	protected $editable;
	protected $canEdit;
	protected $canDelete;
	protected $tabsData = [];
	protected $allTabs = [];
	protected $visibleTabs = [];
        
        /**
         * Array for sub-grids tabs
         * @var array
         */
	protected $subgridTabs = [];
        
	public function __construct($listId, $itemId = null)
	{
		$this->listId = $listId;
		$this->itemId = $itemId;
		$this->formUid = Uuid::generate(4);
		$this->tabUid = Uuid::generate(4);
		$this->params = $this->getFormParams();
		$this->formFields = $this->getFormFields();
		$this->allTabs = $this->getFormTabs();
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
        
        /**
         * Returns tab buttons HTML for sub-grid tabs
         * 
         * @param string $set_name Tabs set name - for example, it can be sub-menu items set
         * @param array $arr_titles Array with tab titles by which tabs will be selected from db: dx_forms_tabs.title
         * @param boolean $is_submenu If 1 then is submenu items (will be used tabdrop logic with popup sub-menu)
         * @return string HTML for sub-grid tab buttons
         */
        public function renderSubgridTabButtons($set_name, $arr_titles, $is_submenu) {
                $result = view('forms.tab_buttons', [
			'itemId' => $this->itemId,
			'formId' => $this->params->form_id,
			'formUid' => $this->formUid,
			'tabId' => $this->tabUid,
			'tabs' => $this->getSubgridTabs($set_name, $arr_titles),
                        'is_tabdrop' => $is_submenu,
		])->render();
		
		return $result;
        }
	
	public function renderTabButtons()
	{
		$result = view('forms.tab_buttons', [
			'itemId' => $this->itemId,
			'formId' => $this->params->form_id,
			'formUid' => $this->formUid,
			'tabId' => $this->tabUid,
			'tabs' => $this->getVisibleTabs(),
                        'is_tabdrop' => 0,
		])->render();
		
		return $result;
	}
        
        /**
         * Returns tab content HTML for sub-grid tabs
         * 
         * @param string $set_name Tabs set name - for example, it can be sub-menu items set
         * @param array $arr_titles Array with tab titles by which tabs will be selected from db: dx_forms_tabs.title
         * @param boolean $is_submenu If 1 then is submenu items (will be used tabdrop logic with popup sub-menu)
         * @return string HTML for sub-grid tab content
         */
        public function renderSubgridTabContents($set_name, $arr_titles, $is_submenu)
	{
		$result = view('forms.tab_contents', [
			'itemId' => $this->itemId,
			'formId' => $this->params->form_id,
			'formUid' => $this->formUid,
			'tabId' => $this->tabUid,
			'tabs' => $this->getSubgridTabs($set_name, $arr_titles),
                        'is_tabdrop' => $is_submenu,
		])->render();
		
		return $result;
	}
	
	public function renderTabContents()
	{
		$result = view('forms.tab_contents', [
			'itemId' => $this->itemId,
			'formId' => $this->params->form_id,
			'formUid' => $this->formUid,
			'tabId' => $this->tabUid,
			'tabs' => $this->getVisibleTabs(),
                        'is_tabdrop' => 0,
		])->render();
		
		return $result;
	}
	
	public function renderField($name)
	{
		$result = '';
		
		foreach($this->formFields as $row)
		{
			if($row->db_name != $name)
				continue;
			
			$field = new FormField($row, $this->listId, $this->itemId, 0, 0, $this->itemData, $this->formUid);
                        $field->is_item_editable = ($this->editable);
			$field->is_disabled_mode = $this->disabled;
			$field->is_editable_wf = $this->editable;
			
			$result = $field->get_field_input_htm();

			break;
		}
		
		return $result;
	}
	
	public function renderScripts()
	{
		
	}
        
        /**
         * Returns array with tabs by provided tabs titles
         * 
         * @param string $set_name Tabs set name - for example, it can be sub-menu items set
         * @param array $arr_titles Array with tab titles by which tabs will be selected from db: dx_forms_tabs.title
         * @return array Array with matching tabs
         */
        protected function getSubgridTabs($set_name, $arr_titles)
	{
		$tab_store = ($set_name) ? $set_name : "_default";
                
                if (isset($this->subgridTabs[$tab_store])) {
                    return $this->subgridTabs[$tab_store];
                }
                
                $this->subgridTabs[$tab_store] = [];
                		
		foreach($this->allTabs as $tab)
		{
			// Skip tabs that are not listed
			if(!empty($arr_titles) && !in_array($tab->title, $arr_titles))
				continue;
			
			$tab->data_htm = '';			
			$this->subgridTabs[$tab_store][$tab->id] = $tab;
		}
                
                return $this->subgridTabs[$tab_store];
        }
	
	/**
	 * Get visible tabs and populate them with form fields html.
	 *
	 * The names of visible tabs must be listed in $this->tabList[] array before calling this function:
	 *
	 * ```php
	 * $this->tabList = ['General', 'Personal details', 'Work details'];
	 * ```
	 *
	 * @return array Array with data of tabs
	 */
	protected function getVisibleTabs()
	{
		if(!empty($this->visibleTabs))
			return $this->visibleTabs;
		
		foreach($this->allTabs as $tab)
		{
			// Skip tabs that are not listed in tabList array
			if(!empty($this->tabList) && !in_array($tab->title, $this->tabList))
				continue;
			
			$tab->data_htm = '';
			
			$this->visibleTabs[$tab->id] = $tab;
		}
		
		// Loop over all fields and distribute them to according tabs
		foreach($this->formFields as $row)
		{
			// skip ID field for new item form
			if($row->db_name == "id" && !$this->itemId)
			{
				continue;
			}
			
			if(in_array($row->db_name, $this->skipFields))
				continue;
			
			$field = new FormField($row, $this->listId, $this->itemId, 0, 0, $this->itemData, $this->formUid);
			$field->is_disabled_mode = $this->disabled;
			$field->is_editable_wf = $this->editable;
			
			if(!$row->tab_id)
				continue;
			
			if(!isset($this->visibleTabs[$row->tab_id]))
				continue;
			
			$this->visibleTabs[$row->tab_id]->data_htm .= $field->get_field_htm();
		}
		
		return $this->visibleTabs;
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
		$fields = \App\Libraries\DBHelper::getFormFields($this->params->form_id);
		
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