<?php

namespace App\Libraries
{

    use DB;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Support\Facades\Schema;
    use App\Libraries\Structure;
    use Illuminate\Support\Facades\File;
    use Auth;
    use App\Exceptions;
    
    /**
     * Palīgfunkciju klase datu bāzes struktūras izveidei
     */
    class DBHelper
    {
        /**
         * Table object ID - for table dx_docs (rwcord ID from tables dx_objects)
         */
        const OBJ_DX_DOC = 140;

        /**
         * Reģistra lauka tips - teksts (no tabulas dx_field_types)
         */
        const FIELD_TYPE_TEXT = 1;
        
        /**
        * Reģistra lauka tips - datums un laiks (no tabulas dx_field_types)
        */
        const FIELD_TYPE_DATETIME = 2;

        /**
         * Reģistra lauka tips - saistītais ieraksts (no tabulas dx_field_types)
         */
        const FIELD_TYPE_RELATED = 3;

        /**
         * Reģistra lauka tips - garš teksts (no tabulas dx_field_types)
         */
        const FIELD_TYPE_LONG_TEXT = 4;

        /**
         * Reģistra lauka tips - skaitlis (no tabulas dx_field_types)
         */
        const FIELD_TYPE_INT = 5;
        
        /**
         * Reģistra lauka tips - ID (no tabulas dx_field_types)
         */
        const FIELD_TYPE_ID= 6;

        /**
         * Reģistra lauka tips - jā/nē (no tabulas dx_field_types)
         */
        const FIELD_TYPE_YES_NO = 7;

        /**
         * Reģistra lauka tips - uzmeklēšanas ieraksts (no tabulas dx_field_types)
         */
        const FIELD_TYPE_LOOKUP = 8;

        /**
         * Reģistra lauka tips - datums (no tabulas dx_field_types)
         */
        const FIELD_TYPE_DATE = 9;

        /**
         * Register field type - file (from table dx_field_types)
         */
        const FIELD_TYPE_FILE = 12;
        
        /**
         * Register field type - registration number (from table dx_field_types)
         */
        const FIELD_TYPE_REG_NR = 13;
        
        /**
         * Register field type - multilevel (from table dx_field_types)
         */
        const FIELD_TYPE_MULTILEVEL = 14;
        
        /**
         * Register field type - color picker (from table dx_field_types)
         */
        const FIELD_TYPE_COLOR = 17;        
               
        /**
         * Register field type - textual value from items list (from table dx_field_types)
         */
        const FIELD_TYPE_REL_TXT = 22;
        
        /**
         * Field operation ID - value from table dx_field_operations
         */
        const FIELD_OPERATION_EQUAL = 1;
        
        /**
         * Returns object row by list_id
         * 
         * @param integer $list_id Registera ID
         * @return object Object row from table dx_objects
         */
        public static function getListObject($list_id) {
            return DB::table("dx_lists as l")
                   ->select('o.*', 'l.id as list_id')
                   ->leftJoin('dx_objects as o', 'l.object_id', '=', 'o.id')
                   ->where('l.id', '=', $list_id)
                   ->first();
        }
        
        /**
         * Returns list row by provided table name
         * @param string $table_name Table name
         * @return object List row
         */
        public static function getListByTable($table_name)
        {
            $obj = DB::table('dx_objects')->where('db_name', '=', $table_name)->first();
            
            if (!$obj) {
                return null;
            }
            
            $list = DB::table('dx_lists')->where('object_id', '=', $obj->id)->first();

            return $list;
        }
        
        /**
         * Returns default view row by provided list id
         * @param string $list_id List ID
         * @return object View row
         */
        public static function getDefaultView($list_id)
        {
            return DB::table('dx_views')->where('list_id', '=', $list_id)->where('is_default', '=', 1)->first();
        }

        /**
         * Appends a new field to the form at the end
         * @param integer $list_id Register ID
         * @param integer $fld_id Field ID
         * @param array $arr_vals Array with additional fields setting
         */
        public static function addFieldToForm($list_id, $fld_id, $arr_vals = [])
        {
            $form = DB::table('dx_forms')->where('list_id', '=', $list_id)->first();

            $arr_vals['list_id'] = $list_id;
            $arr_vals['form_id'] = $form->id;
            $arr_vals['field_id'] = $fld_id;
            if (!isset($arr_vals['order_index'])) {
                $arr_vals['order_index'] = (DB::table('dx_forms_fields')->where('form_id', '=', $form->id)->max('order_index') + 10);
            }
            DB::table('dx_forms_fields')->insert($arr_vals);
        }
        
        /**
         * Appends a new field to the form at the end
         * @param integer $list_id Register ID
         * @param integer $fld_id Field ID
         */
        public static function addFieldToFormTab($list_id, $fld_id, $tab_title, $order_index)
        {
            $form = DB::table('dx_forms')->where('list_id', '=', $list_id)->first();
            
            $tab = DB::table('dx_forms_tabs')->where('form_id', '=', $form->id)->where('title', '=', $tab_title)->first();
            
            if (!$tab) {
                $tab_id = DB::table('dx_forms_tabs')->insertGetId([
                    'form_id' => $form->id, 
                    'title' => $tab_title, 
                    'order_index' => (DB::table('dx_forms_tabs')->where('form_id', '=', $form->id)->max('order_index') + 10),
                    'is_custom_data' => 1
                ]);
            }
            else {
                $tab_id = $tab->id;
            }
            
            if ($order_index == 0) {
                $order_index = (DB::table('dx_forms_fields')->where('form_id', '=', $form->id)->max('order_index') + 10);
            }
            
            DB::table('dx_forms_fields')->insert([
                'list_id' => $list_id,
                'form_id' => $form->id,
                'field_id' => $fld_id,
                'tab_id' => $tab_id,
                'order_index' => $order_index
            ]);
        }
        
         /**
         * Appends a new field to the view at the end
         * @param integer $list_id Register ID
         * @param integer $view_id Register ID
         * @param integer $fld_id Field ID
         */
        public static function addFieldToView($list_id, $view_id, $fld_id)
        {
            DB::table('dx_views_fields')->insert([
                'list_id' => $list_id,
                'view_id' => $view_id,
                'field_id' => $fld_id,
                'order_index' => (DB::table('dx_views_fields')->where('view_id', '=', $view_id)->max('order_index') + 10)
            ]);
        }

        /**
         * Drops field from CMS structure and db
         * 
         * @param mixed $table_name Name of db table or list_id
         * @param string  $field_name Field name
         */
        public static function dropField($table_name, $field_name)
        {

            DBHelper::removeFieldCMS($table_name, $field_name);
            
            if (is_numeric($table_name)) {
                $obj = DBHelper::getListObject($table_name);
                $table_name = $obj->db_name;
            }
            
            Schema::table($table_name, function (Blueprint $table) use ($field_name)
            {
                $table->dropColumn([$field_name]);
            });
        }

        /**
         * Drops field and foreign key from CMS structure and db
         * 
         * @param string $table_name Name of db table
         * @param string  $field_name Field name
         */
        public static function dropForeignField($table_name, $field_name)
        {

            DBHelper::removeFieldCMS($table_name, $field_name);

            Schema::table($table_name, function (Blueprint $table) use ($field_name)
            {
                $table->dropColumn([$field_name]);
                $table->dropForeign([$field_name]);
            });
        }

        /**
         * Removes field from CMS structure
         * 
         * @param mixed $table_name Table name or list_id
         * @param string $field_name Field name
         */
        public static function removeFieldCMS($table_name, $field_name)
        {
            if (is_numeric($table_name)) {
                $list = DB::table('dx_lists')->where('id', '=', $table_name)->first();
            }
            else {
                $list = DBHelper::getListByTable($table_name);
            }
            
            $fld = DB::table('dx_lists_fields')->where('list_id', '=', $list->id)->where('db_name', '=', $field_name)->first();
            
            if (!$fld) {
                return;
            }
            
            DB::transaction(function () use ($fld)
            {
                DB::table('dx_views_fields')->where('field_id', '=', $fld->id)->delete();

                DB::table('dx_forms_fields')->where('field_id', '=', $fld->id)->delete();

                DB::table('dx_db_history')->where('field_id', '=', $fld->id)->delete();

                DB::table('dx_lists_fields')->where('id', '=', $fld->id)->delete();
            });
        }
        
        /**
         * Delete register by table name
         * 
         * @param mixed $table_name Table name or list_id
         */
        public static function deleteRegister($table_name) {
            if (is_numeric($table_name)) {
                $list = DB::table('dx_lists')->where('id', '=', $table_name)->first();
            }
            else {
                $list = DBHelper::getListByTable($table_name);
            }
            
            if (!$list) {
                return;
            }
            
            $list_del = new Structure\StructMethod_register_delete();
            $list_del->list_id = $list->id;
            $list_del->doMethod(); 
        }
        
        /**
         * Removes or hides fields from the lists all views (if found)
         * 
         * @param mixed $table_name List's table name or list's ID
         * @param array $flds_arr Array with field names to be removed
         * @param boolean $is_hide_only True - field will be hidden, False - field will be deleted from view
         */
        public static function removeFieldsFromAllViews($table_name, $flds_arr, $is_hide_only) {
            
            if (is_numeric($table_name)) {
                $list = DB::table('dx_lists')->where('id', '=', $table_name)->first();
            }
            else {
                $list = DBHelper::getListByTable($table_name);
            }
            
            if (!$list) {
                return;
            }            
            
            foreach($flds_arr as $fld) {
                $fld_row = DB::table('dx_lists_fields')->where('list_id', '=', $list->id)->where('db_name', '=', $fld)->first();

                if (!$fld_row) {
                    continue;
                }

                if ($is_hide_only) {
                    DB::table('dx_views_fields')->where('list_id', '=', $list->id)->where('field_id', '=', $fld_row->id)->update(['is_hidden' => 1]);
                }
                else {
                    DB::table('dx_views_fields')->where('list_id', '=', $list->id)->where('field_id', '=', $fld_row->id)->delete();
                }
            }            
        }
        
         /**
         * Removes or hides fields from the lists all forms (if found)
         *
         * @param mixed $table_name List's table name or list's ID
         * @param array $flds_arr Array with field names to be removed
         * @param boolean $is_hide_only True - field will be hidden, False - field will be deleted from form
         */
        public static function removeFieldsFromAllForms($table_name, $flds_arr, $is_hide_only) {
            if (is_numeric($table_name)) {
                $list = DB::table('dx_lists')->where('id', '=', $table_name)->first();
            }
            else {
                $list = DBHelper::getListByTable($table_name);
            }
            
            if (!$list) {
                return;
            }
                        
            foreach($flds_arr as $fld) {
                $fld_row = DB::table('dx_lists_fields')->where('list_id', '=', $list->id)->where('db_name', '=', $fld)->first();

                if (!$fld_row) {
                    continue;
                }

                if ($is_hide_only) {
                    DB::table('dx_forms_fields')->where('list_id', '=', $list->id)->where('field_id', '=', $fld_row->id)->update(['is_hidden' => 1]);
                }
                else {
                    DB::table('dx_forms_fields')->where('list_id', '=', $list->id)->where('field_id', '=', $fld_row->id)->delete();
                }
            }     
        }
        
        /**
         * Ads JavaScript to the list form
         * 
         * @param mixed $table_name List's table name or list's ID
         * @param string $file_name File name which is stored in the folder storage/app/updates
         * @param string $description JavaScript short description
         */
        public static function addJavaScriptToForm($table_name, $file_name, $description) {
            if (is_numeric($table_name)) {
                $list = DB::table('dx_lists')->where('id', '=', $table_name)->first();
            }
            else {
                $list = DBHelper::getListByTable($table_name);
            }
            
            if (!$list) {
                return;
            }
            
            // add special JavaScript
            $form_id = DB::table('dx_forms')->where('list_id', '=', $list->id)->first()->id;

            $dir = storage_path() . DIRECTORY_SEPARATOR . "app" . DIRECTORY_SEPARATOR . "updates" . DIRECTORY_SEPARATOR;       
            $file_js = $dir . $file_name;
            $content = File::get($file_js);

            DB::table('dx_forms_js')->insert([
                'title' => $description,
                'form_id' => $form_id,
                'js_code' => $content
            ]);
        }
        
        /**
         * Removes JavaScript from form
         * 
         * @param mixed $table_name List's table name or list's ID
         * @param string $description JavaScript short description
         */
        public static function removeJavaScriptFromForm($table_name, $description) {
            if (is_numeric($table_name)) {
                $list = DB::table('dx_lists')->where('id', '=', $table_name)->first();
            }
            else {
                $list = DBHelper::getListByTable($table_name);
            }
            
            if (!$list) {
                return;
            }
            
            $form_id = DB::table('dx_forms')->where('list_id', '=', $list->id)->first()->id;

            DB::table('dx_forms_js')->where('form_id', '=', $form_id)->where('title','=', $description)->delete();
        }
        
        /**
         * Gets role ID by role name. If not found - creates it
         * @param string $role_name Role name
         * @return integer Role ID
         */
        public static function getOrCreateRoleID($role_name) {            
            $role_row = DB::table('dx_roles')->where('title', '=', $role_name)->first();
            
            if (!$role_row) {
                return DB::table('dx_roles')->insertGetId(['title' => $role_name]);
            }
            
            return $role_row->id;
        }
        
        /**
         * Locks item so other users can't edit it
         * 
         * @param integer $list_id Register ID
         * @param integer $item_id Item ID
         */
        public static function lockItem($list_id, $item_id) {        
            
            if (DBHelper::isItemLocked($list_id, $item_id)) {
                return; // item is allready locked by this user
            }
            
            DB::table('dx_locks')->insert([
                'list_id' => $list_id,
                'item_id' => $item_id,
                'user_id' => Auth::user()->id,
                'locked_time' => date('Y-n-d H:i:s')
            ]);
        }
        
        public static function isItemLocked($list_id, $item_id) {
            $row = DB::table('dx_locks as l')
                    ->select('l.user_id', 'u.display_name', 'l.locked_time')
                    ->join('dx_users as u', 'l.user_id', '=', 'u.id')
                    ->where('l.list_id', '=', $list_id)
                    ->where('l.item_id', '=', $item_id)
                    ->first();
            
            if ($row) {
                
                if ($row->user_id == Auth::user()->id) {
                    // item locked by this user - this is ok
                    return true;
                }
                
                // item allready locked by another user
                throw new Exceptions\DXCustomException(sprintf(trans('errors.item_locked'), long_date($row->locked_time), $row->display_name, $row->display_name));
            }
            
            // item is not locked jet
            return false;
        }
        
        public static function isItemLockedStatus($list_id, $item_id) {
            $row = DB::table('dx_locks as l')
                    ->select('l.user_id', 'u.display_name', 'l.locked_time')
                    ->join('dx_users as u', 'l.user_id', '=', 'u.id')
                    ->where('l.list_id', '=', $list_id)
                    ->where('l.item_id', '=', $item_id)
                    ->first();
            
            if ($row) {
                return true;
            }
            
            // item is not locked jet
            return false;
        }
        
        /**
         * Unlocks item so other users can edit it
         * 
         * @param integer $list_id Register ID
         * @param integer $item_id Item ID
         */
        public static function unlockItem($list_id, $item_id) {
            
            if (!DBHelper::isItemLocked($list_id, $item_id)) {
                return; // item was not locked (maybe cron JOB released lock)
            }
            
            DB::table('dx_locks')
                    ->where('list_id', '=', $list_id)
                    ->where('item_id', '=', $item_id)
                    ->where('user_id', '=', Auth::user()->id)
                    ->delete();
        }      
        
        /**
         * Returns array with form fields
         * 
         * @param integer $form_id Form ID
         * @param integer $field_id Field ID, not required, if provided then 1 field will be returned
         * @param integer $is_multi_field Indicates which fields to retrieve (-1: all, 0 - non-multi fields, 1 - multi fields)
         * @param string  $ignore_fields Ignorable fields names in apostrofes seperated by coma, for example: 'field1', 'field2'
         * @return Array
         */
        public static function getFormFields($form_id, $field_id = 0, $is_multi_field = -1, $ignore_fields = '') {
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
                    ff.group_label,
                    rt.code as row_type_code,
                    lf.is_right_check,
                    lf.list_id,
                    lf.is_crypted,
                    l.masterkey_group_id,
                    lf.items,
                    o.is_history_logic,
                    lf.is_public_file,
                    lf.numerator_id,
                    ff.is_readonly,
                    lf.is_clean_html,
                    lf.is_text_extract,
                    lf.is_fields_synchro
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
                    left join dx_rows_types rt on ff.row_type_id = rt.id
            WHERE
                    ff.form_id = :form_id";
            
            if ($field_id) {
                $arr_where['field_id'] = $field_id;
                $sql .= " AND ff.field_id = :field_id";
            }
            
            if ($is_multi_field != -1) {
                $sql .= " AND lf.is_multiple_files = " . $is_multi_field;
            }
            
            if ($ignore_fields) {
                $sql .= " AND lf.db_name not in (" . $ignore_fields . ")";
            }
            
            $sql .= "
                ORDER BY
                        ff.order_index
            ";

            $arr_where = array('form_id' => $form_id);
            
            if ($field_id) {
                $arr_where['field_id'] = $field_id;
            }
            
            return DB::select($sql, $arr_where);
        }
        
        /**
         * Updates form field properties for given list by field name
         * 
         * @param integer $list_id Register ID
         * @param string $field_name Field name in db
         * @param array $arr_prop Properties array to be updated (fields from table dx_forms_fields
         */
        public static function updateFormField($list_id, $field_name, $arr_prop) {
            $fld_id = DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('db_name', '=', $field_name)
                    ->first()
                    ->id;
            
            DB::table('dx_forms_fields')
                    ->where('field_id', '=', $fld_id)
                    ->update($arr_prop);

        }
        
        /**
         * Place field after provided field - change order_index
         * 
         * @param integer $list_id Register ID
         * @param string $field_name Field name to be reordered
         * @param string $field_after_name Field name after which field must be placed
         */
        public static function reorderFormField($list_id, $field_name, $field_after_name) {
            $form_fields = DB::table('dx_forms_fields')
                            ->where('list_id', '=', $list_id)
                            ->orderBy('order_index')
                            ->get();
            
            foreach($form_fields as $key => $fld) {
                DB::table('dx_forms_fields')
                    ->where('id', '=', $fld->id)
                    ->update(['order_index' => $key*10]);
            }
            
            $fld_id = DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('db_name', '=', $field_name)
                    ->first()
                    ->id;
            
            $fld_after_id = DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('db_name', '=', $field_after_name)
                    ->first()
                    ->id;
            
            $after_order = DB::table('dx_forms_fields')
                            ->where('field_id', '=', $fld_after_id)
                            ->first()->order_index + 5;
            
            DB::table('dx_forms_fields')
                    ->where('field_id', '=', $fld_id)
                    ->update(['order_index' => $after_order]);
        }
        
        /**
         * Returns display text for lookup or dropdown field value
         * 
         * @param integer $item_value ID for lookup item
         * @param array $fld_attr Aray with field attributes
         * @return string Textual value for given lookup ID
         */
        public static function getLookupDisplayText($item_value, $fld_attr)
        {                         
            if ($item_value == 0) {
                return "";
            }
                        
            $val_row =  DB::table($fld_attr->rel_table_name)
                        ->select($fld_attr->rel_field_name . ' as txt')
                        ->where('id', '=', $item_value)
                        ->first();

            $txt_display = "";
            if ($val_row) {
                $txt_display = $val_row->txt;
            }

            return $txt_display;
            
        }
        
         /**
         * Returns array with register pre-defined fields values for inserting in db
         * Registers could have low level WHERE criteria set for several fields - in order to organize user rights
         * 
         * @param integer $list_id Register ID
         * @param array $save_arr Saving array
         * @return array
         */
        public static function getRegisterFieldsPredefined($list_id) {
            // fill register level fields values
            $flds = DB::table('dx_lists_fields')
                    ->where('list_id', '=', $list_id)
                    ->where('operation_id', '=', 1)
                    ->whereNotNull('default_value')
                    ->get();
            
            $save_arr = [];
            foreach($flds as $fld) {
                $save_arr[$fld->db_name] = $fld->default_value;
            }
            
            return $save_arr;
        }
        
    }

}