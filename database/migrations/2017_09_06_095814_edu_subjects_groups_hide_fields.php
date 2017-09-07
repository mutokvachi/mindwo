<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;
use App\Libraries\Structure;

class EduSubjectsGroupsHideFields extends EduMigration
{
    private $table_name = "edu_subjects_groups";
    
        /**
         * Run the migrations.
         *
         * @return void
         */
        public function edu_up()
        {
            $list_id = App\Libraries\DBHelper::getListByTable($this->table_name)->id;  
                    
            DB::transaction(function () use ($list_id){
                App\Libraries\DBHelper::reorderFormField($list_id, "signup_due", "seats_limit");

                // Hide fields
                App\Libraries\DBHelper::updateFormField($list_id, "is_generated", [
                    'is_hidden' => 1,
                    'order_index' => 0,
                    'row_type_id' => 1,
                    'tab_id' => null
                ]);

                App\Libraries\DBHelper::updateFormField($list_id, "approved_time", [
                    'is_hidden' => 1,
                    'order_index' => 0,
                    'row_type_id' => 1,
                    'tab_id' => null
                ]);

                App\Libraries\DBHelper::updateFormField($list_id, "is_published", [
                    'is_hidden' => 1,
                    'order_index' => 0,
                    'row_type_id' => 1,
                    'tab_id' => null
                ]);

                // Adjust fields
                $form = DB::table('dx_forms')->where('list_id', '=', $list_id)->first();
                
                $tab_cancel_id = DB::table('dx_forms_tabs')->insertGetId([
                    'form_id' => $form->id,
                    'title' => trans('db_' . $this->table_name . '.tab_cancel'),
                    'is_custom_data' => 1,
                    'order_index' => 50
                ]);

                App\Libraries\DBHelper::updateFormField($list_id, "canceled_time", ['tab_id' => $tab_cancel_id]);
                App\Libraries\DBHelper::updateFormField($list_id, "canceled_reason", ['tab_id' => $tab_cancel_id]);
 
                $fld_id = DB::table('dx_lists_fields')->insertGetId([
                    'list_id' => $list_id,
                    'db_name' => 'first_publish',
                    'type_id' => App\Libraries\DBHelper::FIELD_TYPE_DATETIME,
                    'title_list' => trans('db_' . $this->table_name . '.first_publish'),
                    'title_form' => trans('db_' . $this->table_name . '.first_publish')
                ]);
                App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['is_hidden' => 1]);
            });
        }
    
        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function edu_down()
        {  
            DB::transaction(function () {
                $list_id = App\Libraries\DBHelper::getListByTable($this->table_name)->id;  
                $form = DB::table('dx_forms')->where('list_id', '=', $list_id)->first();

                App\Libraries\DBHelper::updateFormField($list_id, "canceled_time", ['tab_id' => null]);
                App\Libraries\DBHelper::updateFormField($list_id, "canceled_reason", ['tab_id' => null]);

                DB::table('dx_forms_tabs')
                ->where('form_id', '=', $form->id)
                ->where('title', '=', trans('db_' . $this->table_name . '.tab_cancel'))
                ->delete();

                App\Libraries\DBHelper::removeFieldCMS($this->table_name, "first_publish");
            });
        }
}
