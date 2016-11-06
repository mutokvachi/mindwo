<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

class DxFormsFieldsOneRowFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        
        Schema::create('dx_rows_types', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title', 100)->nullable()->comment = "Title";
            $table->string('code', 100)->nullable()->comment = "Code";

            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });

        DB::table('dx_rows_types')->insert(['id' => 1, 'title' => '1 field per row', 'code' => 'col-lg-12']);
        DB::table('dx_rows_types')->insert(['id' => 2, 'title' => '2 fields per row', 'code' => 'col-lg-6 col-md-6 col-sm-12 col-xs-12']);
        DB::table('dx_rows_types')->insert(['id' => 3, 'title' => '3 fields per row', 'code' => 'col-lg-4 col-md-12 col-sm-12 col-xs-12']);
        
        Schema::table('dx_forms_fields', function (Blueprint $table) {
            $table->integer('row_type_id')->default(1)->nullable()->unsigned()->comment = "Row type";

            $table->index('row_type_id');
            $table->foreign('row_type_id')->references('id')->on('dx_rows_types');
        });        

        DB::table('dx_forms_fields')->update(['row_type_id'=>1]);

        //Make CMS UI

        $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => 'dx_rows_types', 'title' => 'Row types' , 'is_history_logic' => 1]);

        $list_gen = new Structure\StructMethod_register_generate();
        $list_gen->obj_id = $obj_id;
        $list_gen->register_title = "Row types";
        $list_gen->form_title = "Row type";
        $list_gen->doMethod();

        $list = App\Libraries\DBHelper::getListByTable('dx_forms_fields');
        $rel_list = App\Libraries\DBHelper::getListByTable('dx_rows_types');

        $fld_id = DB::table('dx_lists_fields')->insertGetId([
            'list_id' => $list->id,
            'db_name' => 'row_type_id',
            'type_id' => App\Libraries\DBHelper::FIELD_TYPE_RELATED,
            'title_list' => 'Row type',
            'title_form' => 'Row type',
            'default_value' => 1,
            'rel_list_id' => $rel_list->id,
            'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $rel_list->id)->where('db_name','=', 'title')->first()->id
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
        $list = App\Libraries\DBHelper::getListByTable('dx_rows_types');

        App\Libraries\DBHelper::dropField('dx_forms_fields', 'row_type_id');

        Schema::dropIfExists('dx_rows_types');

        $list_del = new Structure\StructMethod_register_delete();
        $list_del->list_id = $list->id;
        $list_del->doMethod();    
        
    }
}
