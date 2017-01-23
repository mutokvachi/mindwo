<?php

namespace App\Libraries {
    
    use DB;
    use Log;
    use Auth;
    use App\Libraries\Rights;
    use App\Exceptions;
    use Config;
    
    class View 
    {  
	public $user_id = 0;
	public $list_id = 0;
	public $view_id = 0;
	public $model = array();
	public $sql_orderby = "";
	public $summaryrows = array();
	public $err = "";
	public $is_multi_registers = 0;
	public $list_obj_db_name = "";
	public $rel_field_id= 0;
	public $rel_field_value = 0;
	public $sql_user_rights = "";
	public $is_history_logic = 0;
	public $email_fld_arr = array();
	public $rel_formula = "";
	public $grid_title = "";
        public $is_rights_check_off = 0;
        
        /**
         * Include hidden fields in the view model array
         * @var boolean 1 - include, 0 - dont include 
         */
        public $is_hidden_in_model = 0;
        
        public function __construct($list_id, $view_id, $user_id)
        {
            $this->list_id = $list_id;
            $this->view_id = $view_id;
            $this->user_id = $user_id;
        }  

        /**
         * Uzstāda skata objekta parametru vērtības
         * @return int
         */
        private function setViewData() {
            $sql = 	"
			SELECT
				l.list_title,
				o.db_name,
				w.id as view_id,
				o.is_multi_registers,
				o.is_history_logic
			FROM
				dx_lists l
				inner join dx_objects o on l.object_id = o.id
				inner join dx_views w on w.list_id = :list_id
			WHERE
				     l.id = :list_id2
				AND (w.id = :view_id or (:view_id2 = 0 and w.is_default = 1))
			";
                
            $views_rows = DB::select($sql, array("list_id" => $this->list_id, "list_id2" => $this->list_id, "view_id" => $this->view_id, "view_id2" => $this->view_id));

            if (count($views_rows) == 0)
            {
                $this->err = "List or view not found!";
                return 0;
            }

            $row = $views_rows[0];

            $this->view_id = $row->view_id;
            $this->list_obj_db_name = $row->db_name;
            $this->is_multi_registers = $row->is_multi_registers;
            $this->is_history_logic = $row->is_history_logic;
            $this->grid_title = $row->list_title;
        }
        
	public function check_rights()
	{		
		
		// Check user rights
		$sql = "
			SELECT 
				rl.list_id,
				lf.db_name as user_field_name 
			FROM 
				dx_users_roles ur 
				inner join dx_roles_lists rl on ur.role_id = rl.role_id
				left join dx_lists_fields lf on rl.user_field_id = lf.id 
			WHERE 
				ur.user_id = :user_id
				AND rl.list_id = :list_id
			ORDER BY
				lf.db_name
			";	
		
		$rights_rows = DB::select($sql, array("user_id" => $this->user_id, "list_id" => $this->list_id));
                		
                $this->is_rights_on_list = 0;
                foreach($rights_rows as $row)                
                {
                        $this->is_rights_on_list = 1;
                        if (strlen($row->user_field_name) == 0)
                        {
                                // User have rights on list in some role and without field values restriction
                                // So, we dont check other field values and no where criteria will be based on user id
                                break;
                        }

                        if (strlen($this->sql_user_rights) > 0)
                        {
                                $this->sql_user_rights = $this->sql_user_rights . ",";
                        }
                        $this->sql_user_rights = $this->sql_user_rights . $this->list_obj_db_name . "." . $row->user_field_name;
                }

                if (strlen($this->sql_user_rights) > 0)
                {
                        $this->sql_user_rights = " AND " . $this->user_id . " in (" . $this->sql_user_rights . ")";
                }

                if ($this->is_rights_on_list == 0)
                {
                        // Normaly cant be, because list is not available in menu or tabs, but in dirrect URL execution via AJAX case this will prevent from hackers
                        $this->err = "Lietotājam nav tiesību uz reģistra datiem!";
                        return 0;
                }		
		
		return 1;
	}

	private function get_data_model_aggreg($grid_list_id, $rel_list_id, $parent_alias_tb, $init_child_alias_tb, &$out_err, &$out_alias_tb, &$iter, &$out_alias_rel_fld)
	{
		$sql_r = "
			SELECT 
				o.db_name as parent_table,
				m.parent_list_id,
				o_c.db_name as child_table,
				m.child_list_id,
				lf.db_name as rel_field_name,
				m.child_rel_field_id,
				o.is_multi_registers as is_parent_multi,
				o_c.is_multi_registers as is_child_multi
			FROM 
				dx_model m
				INNER JOIN dx_lists l on m.parent_list_id = l.id
				INNER JOIN dx_objects o on l.object_id = o.id
				INNER JOIN dx_lists l_c on m.child_list_id = l_c.id
				INNER JOIN dx_objects o_c on l_c.object_id = o_c.id
				INNER JOIN dx_lists_fields lf on m.child_rel_field_id = lf.id
			WHERE 
				m.child_list_id = " . $rel_list_id . "
			";
		
                $rows = DB::select($sql_r);
                
		if (count($rows) == 0)
                {
                    $out_err = "Incorrect data model for aggregated field detection - list (ID=" . $rel_list_id . ") definition not found!";
                    return "";
                }
		
		$row_r = $rows[0];
		
		$parent_alias = "";
		
		if (strlen($parent_alias_tb) == 0)
		{
			$parent_alias = $row_r->parent_table . "_prnt_" . $iter;//str_replace("-", "_",GUID());
		}
		else
		{
			$parent_alias = $parent_alias_tb;
		}
			
		$iter = $iter + 1;
		
		if (strlen($out_alias_tb) == 0)
		{
			$child_alias_tb = $row_r->child_table;
			$out_alias_tb = $child_alias_tb;
		}
		else
		{
			$child_alias_tb = $row_r->child_table . "_cld_" . $iter;
		}
		
		$out_alias_rel_fld = $row_r->rel_field_name;
		
		if ($row_r->parent_list_id == $grid_list_id)
		{
	
			$sql_rez = ""; // LEFT JOIN " . $row_r["parent_table"] . " " . $parent_alias . " ON " . $init_child_alias_tb . "." . $row_r["rel_field_name"] . " = " . $parent_alias . ".id ";	
					
			if ($row_r->is_child_multi == 1)
			{
				$sql_rez = $sql_rez . " WHERE " . $child_alias_tb . ".multi_list_id = " . $row_r->child_list_id . " ";
			}
			return $sql_rez;
		}
		else
		{
			
			$sql_rez = " LEFT JOIN " . $row_r->parent_table . " " . $parent_alias . " ON " . $child_alias_tb . "." . $row_r->rel_field_name . " = " . $parent_alias . ".id ";
			
			if ($row_r->is_parent_multi == 1)
			{
				$sql_rez = $sql_rez . " AND " . $parent_alias . ".multi_list_id = " . $row_r->parent_list_id . " ";
			}
			
			return $this->get_data_model_aggreg($grid_list_id, $row_r->child_list_id, $child_alias_tb, $init_child_alias_tb, $out_err, $out_alias_tb, $iter, $out_alias_rel_fld) . " " . $sql_rez;
		}
		
	}
	
	private function get_data_model($grid_list_id, $rel_list_id, $parent_alias_tb, $init_child_alias_tb, &$out_err, &$out_alias_tb, &$iter)
	{
		$sql_r = "
			SELECT 
				o.db_name as parent_table,
				m.parent_list_id,
				o_c.db_name as child_table,
				m.child_list_id,
				lf.db_name as rel_field_name,
				m.child_rel_field_id,
				case when m.child_list_id = " . $grid_list_id . " then 1 else 0 end as is_this_list
			FROM 
				dx_model m
				INNER JOIN dx_lists l on m.parent_list_id = l.id
				INNER JOIN dx_objects o on l.object_id = o.id
				INNER JOIN dx_lists l_c on m.child_list_id = l_c.id
				INNER JOIN dx_objects o_c on l_c.object_id = o_c.id
				INNER JOIN dx_lists_fields lf on m.child_rel_field_id = lf.id
			WHERE 
				m.parent_list_id = " . $rel_list_id . "
			ORDER BY is_this_list DESC
			LIMIT 0, 1
			";
		
		$rows = DB::select($sql_r);
		
                if (count($rows) == 0)
                {
                    $out_err = "Incorrect data model - list (ID=" . $rel_list_id . ") definition not found!";
                    return "";
                } 
                
                $row_r = $rows[0];
                
		$parent_alias = "";
		
		if (strlen($parent_alias_tb) == 0)
		{
			$parent_alias = $row_r->parent_table . "_prnt_" . $iter;//str_replace("-", "_",GUID());
		}
		else
		{
			$parent_alias = $parent_alias_tb;
		}
			
		if (strlen($out_alias_tb) == 0)
		{
			$out_alias_tb = $parent_alias;
		}
			
		$child_alias_tb = $row_r->child_table . "_cld_" . $iter; //str_replace("-", "_",GUID());
		$iter = $iter + 1;
		
		if ($row_r->child_list_id == $grid_list_id)
		{
	
			$sql_rez = " LEFT JOIN " . $row_r->parent_table . " " . $parent_alias . " ON " . $init_child_alias_tb . "." . $row_r->rel_field_name . " = " . $parent_alias . ".id ";	
			return $sql_rez;
		}
		else
		{
			
			$sql_rez = " LEFT JOIN " . $row_r->parent_table . " " . $parent_alias . " ON " . $child_alias_tb . "." . $row_r->rel_field_name . " = " . $parent_alias . ".id ";
			return $this->get_data_model($grid_list_id, $row_r->child_list_id, $child_alias_tb, $init_child_alias_tb, $out_err, $out_alias_tb, $iter) . " " . $sql_rez;
		}
		
	}

	public function get_view_sql()
	{
		$this->setViewData();
                
                if ($this->is_rights_check_off == 0 && $this->check_rights() == 0)
		{
                     throw new Exceptions\DXCustomException("Jums nav nepieciešamo tiesību reģistrā ar ID " . $this->list_id . "!");
		}
		
                $view_row = DB::table('dx_views')->where('id','=',$this->view_id)->first();
                
		// Prepare fields set
		$sql = "
			SELECT
				lf.id,
				lf.db_name,
				ifnull(vf.alias_name,lf.title_list) as title_list,
				vf.width,
				vf.align,
				ft.is_date,
				ft.is_integer,
				ft.is_decimal,
				o.db_name as rel_table_db_name,
				rf.db_name as rel_field_db_name,
				vf.is_item_link,
				ft.sys_name,
				lf.formula,
				lf.list_id,
				fo.sys_name as operation,
				vf.criteria,
				vf.is_hidden,
				st.sys_name as order_by,
				vf.is_sum,
				at.sys_name as aggregation,
                                lf.rel_list_id
			FROM
				dx_views_fields vf
				inner join dx_lists_fields lf on vf.field_id = lf.id
				inner join dx_field_types ft on lf.type_id = ft.id
				left join dx_lists_fields rf on lf.rel_display_field_id = rf.id
				left join dx_lists rl on rl.id = lf.rel_list_id
				left join dx_objects o on rl.object_id = o.id
				left join dx_field_operations fo on vf.operation_id = fo.id
				left join dx_sort_types st on vf.sort_type_id = st.id
				left join dx_aggregation_types at on vf.aggregation_id = at.id
			WHERE
				vf.view_id = :view_id
			ORDER BY
				vf.order_index
			";
		
                $fields = DB::select($sql, array('view_id' => $this->view_id));
		
		$sql_fields = "";
		$fld_name="";
		$sql_join = "";
		$rel_cnt = 0;
		$sql_tab_where = "";
		$iter = 0;
		$aggreg = 0;
		$sql_filter = "";
		$sql_rights_join = "";
		$sql_rights_where = "";
		$rights_cnt = 0;
		
		$arr_aggreg = array(); // here we store table names which are allready included for FIRST, LAST aggregations, so not to double and improve performance
		
		$arr_dat = array();
		
		// Add edit icon
		$arr_fld_opt = array(
				"field_id" => 0,
				"name" => "",
				"label" => "",
				"align" => "center",
				"width" => 30,
				"search" => false,
				"sortable" => false,
				"formatter" => "js:myEditBtn",
				"fixed" => true
				);
		array_push($this->model, $arr_fld_opt);
		
                foreach($fields as $row)
		{	
			if (strlen($sql_fields)>0)
			{
				$sql_fields = $sql_fields . ",";
			}
			
			$original_field = ""; // Will be used for filtering and order by logic, this name can be different (can be formula) from field alias name for grid column			                     
                        
			if ($row->list_id != $this->list_id)
			{
				// Check relations from data model
				// get_data_model($grid_list_id, $rel_list_id, $iter_count, $uniq_prefix, &$out_err, &$out_alias_tb)
				
				if (strlen($row->aggregation) == 0)
				{
					$alias_tb = "";
					$sql_join = $sql_join . $this->get_data_model($this->list_id, $row->list_id, "", $this->list_obj_db_name, $this->err, $alias_tb, $iter);
					
					if (strlen($this->err) > 0)
					{
						return"";
					}
					
					$sql_fields =  $sql_fields . $alias_tb . "." . $row->db_name . " as " . $alias_tb . "_" . $row->db_name;
					
					$fld_name = $alias_tb . "_" . $row->db_name;
							
					$original_field = $alias_tb . "_" . $row->db_name;
					
					//Rights on items
					$rights_cnt++;
					$sql_rights_join = $sql_rights_join . " 
					left join (
						SELECT DISTINCT 
							list_item_id 
						FROM
							dx_item_access
						WHERE 
							list_id = " . $row->list_id . ") ia_all_" . $rights_cnt . " on " . $alias_tb . ".id = ia_all_" . $rights_cnt . ".list_item_id		
					left join (
						SELECT DISTINCT 
							list_item_id 
						FROM
							dx_item_access
						WHERE 
							list_id = " . $row->list_id . " 
							AND user_id = " . $this->user_id . ") ia_" . $rights_cnt . " on " . $alias_tb  . ".id = ia_" . $rights_cnt . ".list_item_id
					";
					
					$sql_rights_where = $sql_rights_where . " AND (ia_all_" . $rights_cnt . ".list_item_id is null or ia_" . $rights_cnt . ".list_item_id is not null)";
					
		
				}
				else
				{
					$aggr_oper = $row->aggregation;
								
					$aggreg ++;
					
					$alias_tb = "";
					
					if ($aggr_oper == "MAX" || $aggr_oper == "MIN")
					{
						if (isset($arr_aggreg[$row->list_id]))
						{
							$alias_tb = $arr_aggreg[$row->list_id];
						}
						else
						{
							
							$alias_rel_fld = "";
							
							$aggreg_from_sql = $this->get_data_model_aggreg($this->list_id, $row->list_id, "", $this->list_obj_db_name, $this->err, $alias_tb, $iter, $alias_rel_fld);
		
							$aggreg_from_sql = " LEFT JOIN (SELECT " . $aggr_oper . "(id) as id, " . $alias_rel_fld . " as rel_id FROM " . $alias_tb . $aggreg_from_sql . " GROUP BY " . $alias_rel_fld. ") rel_m" . $aggreg . " ON rel_m" . $aggreg . ".rel_id = " . $this->list_obj_db_name . ".id LEFT JOIN $alias_tb rel_" . $aggreg . " ON rel_m" . $aggreg . ".id = rel_". $aggreg . ".id";
							
							$sql_join = $sql_join . $aggreg_from_sql; 
							
							if (strlen($this->err) > 0)
							{
								return "";
							}
							
							$alias_tb = "rel_" . $aggreg;
						}
					}
					else
					{
							$alias_rel_fld = "";
							
							$aggreg_from_sql = $this->get_data_model_aggreg($this->list_id, $row->list_id, "", $this->list_obj_db_name, $this->err, $alias_tb, $iter, $alias_rel_fld);
		
							$aggreg_from_sql = " LEFT JOIN (SELECT " . $aggr_oper . "(id) as id, " . $alias_rel_fld . " as rel_id FROM " . $alias_tb . $aggreg_from_sql . " GROUP BY " . $alias_rel_fld. ") rel_m" . $aggreg . " ON rel_m" . $aggreg . ".rel_id = " . $this->list_obj_db_name . ".id LEFT JOIN $alias_tb rel_" . $aggreg . " ON rel_m" . $aggreg . ".id = rel_". $aggreg . ".id";
							
							$sql_join = $sql_join . $aggreg_from_sql; 
							
							if (strlen($this->err) > 0)
							{
								return "";
							}
							
							$alias_tb = "rel_" . $aggreg;
					}
					
					$sql_fields =  $sql_fields . $alias_tb . "." . $row->db_name . " as " . $alias_tb . "_" . $row->db_name . "_" . $aggreg;
					
					$fld_name = $alias_tb . "_" . $row->db_name . "_" . $aggreg;
							
					$original_field = $alias_tb . "_" . $row->db_name . "_" . $aggreg;
					
					$arr_aggreg[$row->list_id] = $alias_tb;
					
								
				}
			}
			else if (strlen($row->rel_table_db_name) > 0)
			{
				$rel_cnt++;
				
				$fld_name = $row->rel_table_db_name . "_" . $rel_cnt . "_" . $row->rel_field_db_name;
				
				$sql_fields =  $sql_fields . $row->rel_table_db_name . "_" . $rel_cnt . "." . $row->rel_field_db_name . " as " . $fld_name;
				
                                if ($row->list_id == Config::get('dx.employee_list_id', 0)) {
                                    // ignore supervision rules for related employees in employees list
                                    // because we need to see related manager
                                    $superv_sql = "";
                                    $join_type = " LEFT JOIN ";
                                }
                                else {
                                    $superv_sql = Rights::getSQLSuperviseRights($row->rel_list_id, $row->rel_table_db_name . "_" . $rel_cnt);                                
                                    $join_type = (strlen($superv_sql) > 0) ? " JOIN " : " LEFT JOIN ";
                                }
                                
				$sql_join = $sql_join . $join_type . $row->rel_table_db_name . " " . $row->rel_table_db_name . "_" . $rel_cnt . " ON " . $row->rel_table_db_name . "_" . $rel_cnt . ".id = " . $this->list_obj_db_name . "." . $row->db_name . $superv_sql;
				
				$original_field = $fld_name; 			
			}
			else
			{
				if ($row->db_name == "id")
				{
					$fld_name = "id";
					$sql_fields =  $sql_fields . $this->list_obj_db_name . "." . $row->db_name . " as id";
					
					$original_field = "id";
				}
				else
				{
					if ($view_row->view_type_id != 9)
                                        {
                                            $fld_name = $this->list_obj_db_name . "_" . $row->db_name;
                                        }
                                        else
                                        {
                                            $fld_name = $row->db_name;
                                        }
                                        
                                        $formula = "";
					
					if (strlen($row->formula) > 0)
					{
						$formula = $row->formula;
						
						preg_match_all('/\[(.*?)\]/', $formula, $out_arr);
						
						//str_replace(","'",str_replace("->,"'",implode(",", $out_arr[0])))
						$qMarks = str_repeat('?,', count($out_arr[1]) - 1) . '?';
						$sql_f = "SELECT db_name, title_list from dx_lists_fields WHERE list_id = " . $this->list_id . " AND title_list in (" . $qMarks  . ")";
						
                                                $rows_formulas = DB::select($sql_f, $out_arr[1]);
                                                
						foreach($rows_formulas as $row_f)
						{
                                                    $formula = str_replace("[" . $row_f->title_list . "]", $this->list_obj_db_name . "." . $row_f->db_name, $formula);
						}
	
					}
					
					$fld = "";
					if (strlen($formula) > 0)
					{
						$fld = $formula;
					}
					else
					{
						$fld = $this->list_obj_db_name . "." . $row->db_name;
					}
					
                                        $sql_fields =  $sql_fields . $fld . " as " . $fld_name;
										
					if ($row->sys_name == "email")
					{
						array_push ($this->email_fld_arr, $fld_name); // we put all email fields in array which can be used to process email sending logic
					}
					
					$original_field = $fld_name; // $fld;				
				}	
			}
			
			if (strlen($original_field) > 0)
			{
				// Set sorting logic (available only for visible columns)
				if (strlen($row->order_by) > 0 && $row->is_hidden == 0)
				{
					if (strlen($this->sql_orderby) > 0)
					{
						$this->sql_orderby = $this->sql_orderby . ", ";
					}
					
					$this->sql_orderby = $this->sql_orderby . $original_field . " " . $row->order_by;
				}
				
				// Set filtering logic
				if (strlen($row->operation) > 0)
				{
					if ($row->operation == " LIKE") {
                                            $sql_filter = $sql_filter . " AND " . $original_field . $row->operation . " '%" . $row->criteria . "%' ";
                                        }
                                        elseif ($row->operation == " IS NULL" || $row->operation == " IS NOT NULL")
					{
						$sql_filter = $sql_filter . " AND " . $original_field . $row->operation;
					}
					else
					{
						$crit = $row->criteria;
						if ($crit == "[ME]")
						{
							$crit = $this->user_id;
						}
                                                
                                                if ($row->sys_name == "bool") {
                                                    $crit = ($row->criteria == "'" . trans('fields.yes') . "'") ? 1 : 0;
                                                }
						$sql_filter = $sql_filter . " AND " . $original_field . $row->operation . $crit;
					}
				}
								
				if ($row->id == $this->rel_field_id)
				{
					// This is for TAB grid additional WHERE by related id
					$sql_tab_where = " AND " . $this->list_obj_db_name . "." . $row->db_name . " = " . $this->rel_field_value;
				}
				else
				{
					if ($row->is_hidden == 0 || $this->is_hidden_in_model)
					{
						$arr_fld_opt = array(
								"field_id" => $row->id,
								"list_id" => $row->list_id,
								"name" => $fld_name,
								"index" => $fld_name,
								"label" => $row->title_list,
								"align" => $row->align,
								"width" => $row->width,
								"search" => true,
								"sortable" => true,
								"type" => $row->sys_name,
                                                                "is_hidden" => $row->is_hidden
								);
						
						// Grand total logic
						if ($row->is_sum == 1)
						{
							$this->summaryrows[$fld_name] = array($fld_name => "SUM");
						}
										
						if ($row->is_item_link==1)
						{
							$arr_fld_opt["is_link"] = 1;	
						}
						else
						{
							$arr_fld_opt["is_link"] = 0;
						}
						
						if ($row->sys_name == "datetime")
						{
							$arr_fld_opt["formatter"] = "datetime";
							$arr_fld_opt["formatoptions"] = array("srcformat" => "Y-m-d h:i", "newformat" => "d.m.Y H:i");
							$arr_fld_opt["sorttype"] = "date";
							
							array_push($arr_dat, $fld_name);
						}
						
						if ($row->sys_name == "date")
						{
							$arr_fld_opt["formatter"] = "date";
							$arr_fld_opt["formatoptions"] = array("srcformat" => "Y-m-d", "newformat" => "d.m.Y");
							$arr_fld_opt["sorttype"] = "date";
							
							array_push($arr_dat, $fld_name);
						}
						
						if ($row->db_name == "id" || $row->sys_name == "datetime")
						{
							$arr_fld_opt["fixed"] = true;
						}
						
						array_push($this->model, $arr_fld_opt);
					}
				}
			}
			else
			{
				// Field is not included for some reason
				// Remove last coma
				$sql_fields = substr($sql_fields, 0, strlen($sql_fields)-1);			
			}
		}
		
		$sql_multi = "";
		if ($this->is_multi_registers == 1)
		{
			$sql_multi = " AND " . $this->list_obj_db_name . ".multi_list_id = " . $this->list_id;
		}
		
		if ($this->rel_field_id> 0 && strlen($sql_tab_where) == 0)
		{
			$this->err = "Nekorekti nokonfigurēta sistēma - skatā (ID = " . $this->view_id . ") nav iekļauts lauks, pēc kura tiek veikta saistīto ierakstu atlase!";
			return "";
		}
		
                /*
		$sql_hist = "";
		if ($this->is_history_logic == 1)
		{
			$sql_hist = " AND " . $this->list_obj_db_name . ".is_deleted = 0";
		}
		*/
                
                // Check if there is at least one item in the list with item level rights
                $item_level_right = DB::table('dx_item_access')
                                    ->where('list_id','=',$this->list_id)
                                    ->first();
                
                if ($item_level_right)
                {
                    // Special access check
                    $sql_join = $sql_join . " left join dx_item_access ia on " . $this->list_obj_db_name . ".id = ia.list_item_id and ia.user_id=" . $this->user_id . " AND ia.list_id = " . $this->list_id;	
                    $spec_access = " AND (ifnull((select count(*) as sk from dx_item_access where list_id = " . $this->list_id . " AND list_item_id = " . $this->list_obj_db_name . ".id),0)=0 or ia.id is not null)";
                }
                
                // Here we join sqls for related lists
		$sql_join = $sql_join . " " . $sql_rights_join;
		$spec_access = $sql_rights_where;
		
		/*
		// Autocompleate view formula check
		if (strlen($this->rel_formula) > 0)
		{
			$formula = $this->rel_formula;
			
			preg_match_all('/\[(.*?)\]/', $formula, $out_arr);
			
			//str_replace("]","'",str_replace("[","'",implode(",", $out_arr[0])))
			$qMarks = str_repeat('?,', count($out_arr[1]) - 1) . '?';
			
			$sql_f = "SELECT db_name, title_list from dx_views_fields vf inner join dx_lists_fields lf on vf.field_id = lf.id WHERE vf.view_id = " . $this->view_id . " AND isnull(vf.alias_name, lf.title_list) in (" . $qMarks  . ")";
			$sth_f = $this->db_con->prepare($sql_f);
			$result_f = $sth_f->execute($out_arr[1]);
			
			if (!$result_f)
			{
				$this->err = "Wrong SQL for formula fields!";
				return "";
			}
			
			
			while ($row_f = $sth_f->fetch(PDO::FETCH_ASSOC))
			{
				$formula = str_replace("[" . $row_f["title_list"] . "]", $this->list_obj_db_name . "." . $row_f["db_name"], $formula);
			}

		}
		*/
		
		//$grid_sql = "SELECT * FROM (SELECT " . $sql_fields . " FROM " . $this->list_obj_db_name . $sql_join . " WHERE 1=1 " . $sql_multi . $sql_tab_where . $this->sql_user_rights . $spec_access . ") tb WHERE 1=1 " . $sql_filter;
                                
                $grid_sql = "";
                if ($view_row->view_type_id != 9 || strlen($this->sql_user_rights) > 0)
                {
                    /*
                    if ($view_row->is_hidden_from_tabs == 1)
                    {
                        $grid_sql = "SELECT " . $sql_fields . " FROM " . $this->list_obj_db_name . $sql_join . " WHERE 1=1 " . $sql_multi . $sql_tab_where . $this->sql_user_rights . $spec_access . $sql_filter;
                        
                        DB::statement('CREATE OR REPLACE VIEW v_data_' . $this->view_id . ' as ' . $grid_sql);
                        $grid_sql = "SELECT * FROM v_data_" . $this->view_id . " WHERE 1=1 "; 
                    }
                    else
                    {
                    */
                    $superv_sql = "";
                    
                    if ($this->list_id != Config::get('dx.employee_list_id', 0) || !$this->is_rights_check_off) {
                        $superv_sql = Rights::getSQLSuperviseRights($this->list_id, $this->list_obj_db_name);
                    }
                    
                    $grid_sql = "SELECT * FROM (SELECT " . $sql_fields . " FROM " . $this->list_obj_db_name . $sql_join . " WHERE 1=1 " . $this->getListLevelFilter() . $sql_multi . $sql_tab_where . $superv_sql . Rights::getSQLSourceRights($this->list_id, $this->list_obj_db_name) . $this->sql_user_rights . $spec_access . ") tb WHERE 1=1 " . $sql_filter;
                    
                }
                else
                {
                    $grid_sql = $view_row->custom_sql;
                    
                    $grid_sql = str_replace("[ME]", $this->user_id, $grid_sql);
                    $grid_sql = str_replace("[ITEM_ID]", $this->rel_field_value, $grid_sql);
                    
                }
                
                //DB::statement('CREATE OR REPLACE VIEW v_data_' . $this->view_id . ' as ' . $grid_sql);

                //$sql = "SELECT * FROM v_data_" . $this->view_id . " WHERE 1=1 "; 
                Log::info("GRID SQL: " . $grid_sql);
                return $grid_sql;
                
	}
        
        /**
         * Izgūst datu filtrēšanas kritērijus reģistra līmenī (kritērijus var definēt katram reģistra laukam)
         * 
         * @return string Filtrēšanas kritēriji reģistra līmenī
         */
        private function getListLevelFilter() {
            $fields = DB::table('dx_lists_fields as lf')
                      ->select(DB::raw('lf.db_name, lf.criteria, fo.sys_name as list_operation'))
                      ->join('dx_field_operations as fo','lf.operation_id','=','fo.id')
                      ->where('list_id', '=', $this->list_id)
                      ->whereNotNull('operation_id')
                      ->get();
            
            $flt_criteria = "";
            foreach($fields as $fld) {
                $flt_criteria .= $this->getFieldCriteria($fld); 
            }
            
            return $flt_criteria;
        }
        
        /**
         * Izgūst reģistra lauka filtrēšanas kritēriju
         * 
         * @param Object $row Reģistra lauks
         * @return string Reģistra lauka filtrēšanas kritērijs
         */
        private function getFieldCriteria($row) {
            $sql_filter = "";
            $fld_name = $this->list_obj_db_name . "." . $row->db_name;
            if ($row->list_operation == " LIKE") {
                $sql_filter = $sql_filter . " AND " . $fld_name . $row->list_operation . " '%" . $row->criteria . "%' ";
            }
            elseif ($row->list_operation == " IS NULL" || $row->list_operation == " IS NOT NULL")
            {
                    $sql_filter = $sql_filter . " AND " . $fld_name . $row->list_operation;
            }
            else
            {
                    $crit = $row->criteria;
                    if ($crit == "[ME]")
                    {
                            $crit = $this->user_id;
                    }
                    $sql_filter = $sql_filter . " AND " . $fld_name . $row->list_operation . $crit;
            }
            
            return $sql_filter;
        }
        
        
    }    
}