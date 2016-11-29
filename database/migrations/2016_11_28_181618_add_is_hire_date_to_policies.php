<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsHireDateToPolicies extends Migration
{
    private $js_info = 'Show or hide effective date field according to selected hiring field option value';
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        Schema::table('dx_users_accrual_policies', function (Blueprint $table) {
            $table->boolean('is_hiring_date')->default(1)->nullable()->comment = "Start from hiring date";
        });
        
        $list = App\Libraries\DBHelper::getListByTable('dx_users_accrual_policies');
                
        $fld_id = DB::table('dx_lists_fields')->insertGetId([
            'list_id' => $list->id,
            'db_name' => 'is_hiring_date',
            'type_id' => App\Libraries\DBHelper::FIELD_TYPE_YES_NO,
            'title_list' => 'From hiring',
            'title_form' => 'Start from hiring date',
            'default_value' => 1,
        ]);
        
        App\Libraries\DBHelper::addFieldToForm($list->id, $fld_id);
        
        $list_id = $list->id;
        
         // adjust fields in form
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'eff_date')
                                            ->first()->id)
                ->update(['row_type_id' => 3, 'order_index' => 200]);
        
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'end_date')
                                            ->first()->id)
                ->update(['row_type_id' => 3, 'order_index' => '210']);
        
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'is_hiring_date')
                                            ->first()->id)
                ->update(['row_type_id' => 3, 'order_index' => '190']);
        
        // add special JavaScript
        \App\Libraries\DBHelper::addJavaScriptToForm('dx_users_accrual_policies', '2016_11_28_employee_policy.js', $this->js_info);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        App\Libraries\DBHelper::dropField('dx_users_accrual_policies', 'is_hiring_date');
        
        DB::table('dx_forms_js')->where('title', '=', $this->js_info)->delete();
    }
}
