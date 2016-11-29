<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

class CreateAssetsDevices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::dropIfExists('dx_users_assets');
        Schema::dropIfExists('dx_users_assets_types');
                
        DB::table('dx_objects')->whereIn('db_name', ['dx_users_assets', 'dx_users_assets_types'])->delete();
        
        // create card types classifier
        Schema::create('dx_users_assets_types', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->string('title', 100)->nullable()->comment = "Title";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
        DB::table('dx_users_assets_types')->insert([
            ['title' => 'Computer'],
            ['title' => 'Cell phone'],
            ['title' => 'Monitor'],
        ]);
        
        // create cards
        Schema::create('dx_users_assets', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->integer('user_id')->nullable()->comment = "Employee";
            
            $table->integer('asset_type_id')->nullable()->unsigned()->comment = "Asset type";            
            $table->string('title', 500)->nullable()->comment = "Description";
            
            $table->string('serial_nr', 100)->nullable()->comment = "Serial number";
                        
            $table->date('loaned_date')->nullable()->comment = "Date loaned";
            $table->date('return_date')->nullable()->comment = "Date returned";            
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('user_id');            
            $table->foreign('user_id')->references('id')->on('dx_users')->onDelete('cascade');
            
            $table->index('asset_type_id');            
            $table->foreign('asset_type_id')->references('id')->on('dx_users_assets_types');
        });
        
        // get assets menu parent item for classifiers
        $menu_parent_id = DB::table('dx_menu')->where('parent_id', '=', 252)->where('title', '=', 'Assets')->first()->id;

         // create card types classifier register
        $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => 'dx_users_assets_types', 'title' => 'Asset types' , 'is_history_logic' => 1]);
        $list_gen = new Structure\StructMethod_register_generate();
        $list_gen->obj_id = $obj_id;
        $list_gen->register_title = "Asset types";
        $list_gen->form_title = "Asset type";
        $list_gen->doMethod();
        
        // get list
        $list_id = App\Libraries\DBHelper::getListByTable('dx_users_assets_types')->id;       
        
        // rights
        DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins
        DB::table('dx_roles_lists')->insert(['role_id' => 39, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); //HR

        // menu
        DB::table('dx_menu')->insertGetId(['parent_id' => $menu_parent_id, 'title'=>'Asset types', 'list_id'=>$list_id, 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', $menu_parent_id)->max('order_index')+10), 'group_id'=>1, 'position_id' => 1]);
        
        // create cards register
        $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => 'dx_users_assets', 'title' => 'Devices' , 'is_history_logic' => 1]);
        $list_gen = new Structure\StructMethod_register_generate();
        $list_gen->obj_id = $obj_id;
        $list_gen->register_title = "Devices";
        $list_gen->form_title = "Device";
        $list_gen->doMethod();
        
        // get list
        $list_id = App\Libraries\DBHelper::getListByTable('dx_users_assets')->id;       
        
        // rights
        DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins
        DB::table('dx_roles_lists')->insert(['role_id' => 39, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); //HR

        // menu
        $rep_menu = DB::table('dx_menu')->where('title', '=', 'Reports')->first();
        if ($rep_menu) {
            DB::table('dx_menu')->insertGetId(['parent_id' => $rep_menu->id, 'title'=>'Devices', 'list_id'=>$list_id, 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', $rep_menu->id)->max('order_index')+10), 'group_id'=>1, 'position_id' => 1]);
        }
        
        //fix user field (because we have 2 registers in 1 table dx_users)
        DB::table('dx_lists_fields')
                ->where('list_id', '=', $list_id)
                ->where('db_name', '=', 'user_id')
                ->update([
                    'rel_list_id'=>Config::get('dx.employee_list_id'),
                    'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', Config::get('dx.employee_list_id'))->where('db_name', '=', 'display_name')->first()->id,
                    'type_id' => \App\Libraries\DBHelper::FIELD_TYPE_LOOKUP
                ]);        
        
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'loaned_date')
                                            ->first()->id)
                ->update(['row_type_id' => 2]);
        
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'return_date')
                                            ->first()->id)
                ->update(['row_type_id' => 2]);

        // make tab in employee profile form
        $form_id = DB::table('dx_forms')->where('list_id', '=', Config::get('dx.employee_list_id'))->first()->id;
        
        DB::table('dx_forms_tabs')->insert([
            'form_id'=>$form_id,
            'title' => 'Devices',
            'grid_list_id' => $list_id,
            'grid_list_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'user_id')->first()->id,
            'order_index' => (DB::table('dx_forms_tabs')->where('form_id', '=', $form_id)->max('order_index')+10)
        ]);
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $list = App\Libraries\DBHelper::getListByTable('dx_users_assets');
        $list_del = new Structure\StructMethod_register_delete();
        $list_del->list_id = $list->id;
        $list_del->doMethod(); 
        
        $list = App\Libraries\DBHelper::getListByTable('dx_users_assets_types');
        $list_del = new Structure\StructMethod_register_delete();
        $list_del->list_id = $list->id;
        $list_del->doMethod();
        
        Schema::dropIfExists('dx_users_assets');
        Schema::dropIfExists('dx_users_assets_types');
                
        DB::table('dx_objects')->whereIn('db_name', ['dx_users_assets', 'dx_users_assets_types'])->delete();
    }
}
