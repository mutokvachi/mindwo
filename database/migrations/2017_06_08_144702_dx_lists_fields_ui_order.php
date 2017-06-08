<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxListsFieldsUiOrder extends Migration
{ 
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            
            
            $list_id = App\Libraries\DBHelper::getListByTable('dx_lists_fields')->id;
            $form_id = DB::table('dx_forms')
                        ->where('list_id', '=', $list_id)
                        ->first()->id;
            
            $form_flds = DB::table('dx_forms_fields')->where('form_id', '=', $form_id)->get();
            foreach($form_flds as $key => $fld) {
                DB::table('dx_forms_fields')
                        ->where('id', '=', $fld->id)
                        ->update(['order_index' => $key*10]);
            }
            
            $field_id = DB::table('dx_lists_fields')
                        ->where('list_id', '=', $list_id)
                        ->where('db_name', '=', 'db_name')
                        ->first()->id;
            
            $hint_field = DB::table('dx_lists_fields as lf')
                        ->select('ff.order_index')
                        ->join('dx_forms_fields as ff', 'ff.field_id', '=', 'lf.id')
                        ->where('lf.list_id', '=', $list_id)
                        ->where('lf.db_name', '=', 'is_crypted')
                        ->first();
            
            DB::table('dx_forms_fields')
                    ->where('field_id', '=', $field_id)
                    ->update(['order_index' => $hint_field->order_index-5]);
            
            $f_field = DB::table('dx_lists_fields as lf')
                        ->select('ff.order_index')
                        ->join('dx_forms_fields as ff', 'ff.field_id', '=', 'lf.id')
                        ->where('lf.list_id', '=', $list_id)
                        ->where('lf.db_name', '=', 'title_form')
                        ->first();
            
            $field_id = DB::table('dx_lists_fields')
                        ->where('list_id', '=', $list_id)
                        ->where('db_name', '=', 'title_list')
                        ->first()->id;
            
            DB::table('dx_forms_fields')
                    ->where('field_id', '=', $field_id)
                    ->update(['row_type_id' => 2, 'order_index' => $f_field->order_index-5]);
            
            $field_id = DB::table('dx_lists_fields')
                        ->where('list_id', '=', $list_id)
                        ->where('db_name', '=', 'title_form')
                        ->first()->id;
            
            DB::table('dx_forms_fields')
                    ->where('field_id', '=', $field_id)
                    ->update(['row_type_id' => 2]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
