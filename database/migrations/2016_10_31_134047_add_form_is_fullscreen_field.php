<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFormIsFullscreenField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_forms', function (Blueprint $table) {
            $table->boolean('is_full_screen_mode')->default(0)->nullable()->comment = "Is form for opening in full screen mode";
        });
        
        $list = App\Libraries\DBHelper::getListByTable('dx_forms');
                
        $fld_id = DB::table('dx_lists_fields')->insertGetId([
            'list_id' => $list->id,
            'db_name' => 'is_full_screen_mode',
            'type_id' => App\Libraries\DBHelper::FIELD_TYPE_YES_NO,
            'title_list' => trans('form.lbl_full_screen'),
            'title_form' => trans('form.lbl_full_screen'),
            'default_value' => 0,
        ]);
        
        App\Libraries\DBHelper::addFieldToForm($list->id, $fld_id);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        App\Libraries\DBHelper::dropField('dx_forms', 'is_full_screen_mode');
    }
}
