<?php

namespace App\Libraries
{

    use DB;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Support\Facades\Schema;
    use App\Libraries\Structure;
    use Illuminate\Support\Facades\File;
    
    /**
     * Palīgfunkciju klase datu bāzes struktūras izveidei
     */
    class DBHelper
    {

        /**
         * Reģistra lauka tips - teksts (no tabulas dx_field_types)
         */
        const FIELD_TYPE_TEXT = 1;

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
         * Register field type - color picker (from table dx_field_types)
         */
        const FIELD_TYPE_COLOR = 17;
        
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
         */
        public static function addFieldToForm($list_id, $fld_id)
        {
            $form = DB::table('dx_forms')->where('list_id', '=', $list_id)->first();

            DB::table('dx_forms_fields')->insert([
                'list_id' => $list_id,
                'form_id' => $form->id,
                'field_id' => $fld_id,
                'order_index' => (DB::table('dx_forms_fields')->where('form_id', '=', $form->id)->max('order_index') + 10)
            ]);
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
                return;
            }
            
            if ($order_index == 0) {
                $order_index = (DB::table('dx_forms_fields')->where('form_id', '=', $form->id)->max('order_index') + 10);
            }
            
            DB::table('dx_forms_fields')->insert([
                'list_id' => $list_id,
                'form_id' => $form->id,
                'field_id' => $fld_id,
                'tab_id' => $tab->id,
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
         * @param string $table_name DB table name
         */
        public static function deleteRegister($table_name) {
            $list = DBHelper::getListByTable($table_name);
            
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
         * @param string $table_name List's table name
         * @param array $flds_arr Array with field names to be removed
         * @param boolean $is_hide_only True - field will be hidden, False - field will be deleted from view
         */
        public static function removeFieldsFromAllViews($table_name, $flds_arr, $is_hide_only) {
            $list = DBHelper::getListByTable($table_name);
            
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
         * @param string $table_name List's table name
         * @param array $flds_arr Array with field names to be removed
         * @param boolean $is_hide_only True - field will be hidden, False - field will be deleted from form
         */
        public static function removeFieldsFromAllForms($table_name, $flds_arr, $is_hide_only) {
            $list = DBHelper::getListByTable($table_name);
            
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
         * @param string $table_name List's table name
         * @param string $file_name File name which is stored in the folder storage/app/updates
         * @param string $description JavaScript short description
         */
        public static function addJavaScriptToForm($table_name, $file_name, $description) {
            $list = DBHelper::getListByTable($table_name);
            
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
    }

}