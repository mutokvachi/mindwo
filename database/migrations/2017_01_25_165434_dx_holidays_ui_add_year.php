<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

class DxHolidaysUiAddYear extends Migration
{
    private $js_title = 'Show or hide interval end fields acording to option "Is several days"';
    private $table_name = 'dx_holidays';
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         DB::transaction(function () {
           
            $list_id = App\Libraries\DBHelper::getListByTable('dx_holidays')->id;
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'from_year',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
                'title_list' => 'Year',
                'title_form' => 'Year',
                'hint' => 'If not provided, then holiday is valid for all years',
                'max_lenght' => 4
            ]);
        
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['order_index' => 25, 'row_type_id' => 3]); 
            
            DB::table('dx_forms_fields')->where('field_id', '=', DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'from_month_id')->first()->id)->update(['row_type_id' => 3]);
            DB::table('dx_forms_fields')->where('field_id', '=', DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'from_day_id')->first()->id)->update(['row_type_id' => 3]);
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'to_year',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
                'title_list' => 'To year',
                'title_form' => 'To year',
                'hint' => 'If not provided, then holiday is valid for all years',
                'max_lenght' => 4
            ]);
        
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['order_index' => 55, 'row_type_id' => 3]);
            
            DB::table('dx_forms_fields')->where('field_id', '=', DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'to_month_id')->first()->id)->update(['row_type_id' => 3]);
            DB::table('dx_forms_fields')->where('field_id', '=', DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'to_day_id')->first()->id)->update(['row_type_id' => 3]);

            \App\Libraries\DBHelper::removeJavaScriptFromForm($this->table_name, $this->js_title);
            
            \App\Libraries\DBHelper::addJavaScriptToForm($this->table_name, '2017_01_25_dx_holidays.js', $this->js_title);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::transaction(function () {
            $list_id = App\Libraries\DBHelper::getListByTable('dx_holidays')->id;
            
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'from_year');
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'to_year');
            
            DB::table('dx_forms_fields')->where('field_id', '=', DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'from_month_id')->first()->id)->update(['row_type_id' => 2]);
            DB::table('dx_forms_fields')->where('field_id', '=', DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'from_day_id')->first()->id)->update(['row_type_id' => 2]);

            DB::table('dx_forms_fields')->where('field_id', '=', DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'to_month_id')->first()->id)->update(['row_type_id' => 2]);
            DB::table('dx_forms_fields')->where('field_id', '=', DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'to_day_id')->first()->id)->update(['row_type_id' => 2]);

        });
    }
}
