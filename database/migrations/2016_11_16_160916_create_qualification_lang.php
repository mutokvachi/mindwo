<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;
use Illuminate\Support\Facades\Config;

class CreateQualificationLang extends Migration
{
    
    private $is_hr_ui = false;
    private $is_hr_role = false;
    
    private function checkUI_Role() {
        $list_id = Config::get('dx.employee_list_id', 0);
        
        $this->is_hr_ui = ($list_id > 0);   
        
        $this->is_hr_role  = (App::getLocale() == 'en');
    }
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->checkUI_Role();
        
        Schema::dropIfExists('dx_users_langs');
        Schema::dropIfExists('dx_langs');
        Schema::dropIfExists('dx_lang_levels');
        DB::table('dx_objects')->whereIn('db_name', ['dx_users_langs', 'dx_langs', 'dx_lang_levels'])->delete();
        
        Schema::create('dx_lang_levels', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->string('title', 100)->nullable()->comment = "Title";
            $table->string('description', 1000)->nullable()->comment = "Description";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
        Schema::create('dx_langs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->string('title', 100)->nullable()->comment = "Title";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
        DB::table('dx_langs')->insert(['title' => 'English']);
        DB::table('dx_langs')->insert(['title' => 'Russinan']);
        DB::table('dx_langs')->insert(['title' => 'Latvian']);
        
        
        DB::table('dx_lang_levels')->insert(['title' => 'A1 Breakthrough or beginner']);
        DB::table('dx_lang_levels')->insert(['title' => 'A2 Waystage or elementary']);
        DB::table('dx_lang_levels')->insert(['title' => 'B1 Threshold or intermediate']);
        DB::table('dx_lang_levels')->insert(['title' => 'B2 Vantage or upper intermediate']);
        DB::table('dx_lang_levels')->insert(['title' => 'C1 Effective Operational Proficiency or advanced']);
        DB::table('dx_lang_levels')->insert(['title' => 'C2 Mastery or proficiency']);
        
        Schema::create('dx_users_langs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->integer('user_id')->nullable()->comment = "Employee";
            $table->integer('lang_id')->nullable()->unsigned()->comment = "Language";
            $table->integer('level_id')->nullable()->unsigned()->comment = "Level";
                        
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('user_id');            
            $table->foreign('user_id')->references('id')->on('dx_users')->onDelete('cascade');
            
            $table->index('lang_id');  
            $table->foreign('lang_id')->references('id')->on('dx_langs');
            
            $table->index('level_id');  
            $table->foreign('level_id')->references('id')->on('dx_lang_levels');
        }); 
        
        // create language levels register
        $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => 'dx_lang_levels', 'title' => 'Language levels' , 'is_history_logic' => 1]);
        $list_gen = new Structure\StructMethod_register_generate();
        $list_gen->obj_id = $obj_id;
        $list_gen->register_title = "Language levels";
        $list_gen->form_title = "Language level";
        $list_gen->doMethod();
        
        // get list
        $list_id = App\Libraries\DBHelper::getListByTable('dx_lang_levels')->id;       
        
        // rights
        if ($this->is_hr_role) {
            DB::table('dx_roles_lists')->insert(['role_id' => 39, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); //HR
        }
        
        DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins

        // menu
        if ($this->is_hr_ui) {
            DB::table('dx_menu')->insert(['parent_id' => 252, 'title'=>'Language levels', 'list_id'=>$list_id, 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', 252)->max('order_index')+10), 'group_id'=>1, 'position_id' => 1]);
        }
        
        // create languages register        
        $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => 'dx_langs', 'title' => 'Languages' , 'is_history_logic' => 1]);
        $list_gen = new Structure\StructMethod_register_generate();
        $list_gen->obj_id = $obj_id;
        $list_gen->register_title = "Languages";
        $list_gen->form_title = "Language";
        $list_gen->doMethod();
        
        // get list
        $list_id = App\Libraries\DBHelper::getListByTable('dx_langs')->id;       
        
        // rights
        if ($this->is_hr_role) {
            DB::table('dx_roles_lists')->insert(['role_id' => 39, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); //HR
        }
        DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins

        // menu
        if ($this->is_hr_ui) {
            DB::table('dx_menu')->insert(['parent_id' => 252, 'title'=>'Languages', 'list_id'=>$list_id, 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', 252)->max('order_index')+10), 'group_id'=>1, 'position_id' => 1]);
        }
        
        // create employees/languages register        
        $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => 'dx_users_langs', 'title' => 'Employee languages' , 'is_history_logic' => 1]);
        $list_gen = new Structure\StructMethod_register_generate();
        $list_gen->obj_id = $obj_id;
        $list_gen->register_title = "Employee languages";
        $list_gen->form_title = "Employee language";
        $list_gen->doMethod();
                        
        // get list
        $list_id = App\Libraries\DBHelper::getListByTable('dx_users_langs')->id;       
        
        //fix user field (because we have 2 registers in 1 table dx_users)
        if ($this->is_hr_ui) {
            DB::table('dx_lists_fields')
                ->where('list_id', '=', $list_id)
                ->where('db_name', '=', 'user_id')
                ->update([
                    'rel_list_id'=>Config::get('dx.employee_list_id'),
                    'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', Config::get('dx.employee_list_id'))->where('db_name', '=', 'display_name')->first()->id,
                    'type_id' => \App\Libraries\DBHelper::FIELD_TYPE_LOOKUP
                ]);
        }
                
        // rights
        if ($this->is_hr_role) {
            DB::table('dx_roles_lists')->insert(['role_id' => 39, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); //HR
        }
        DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins
        
        if ($this->is_hr_ui) {
            // make tab in employee profile form
            $form_id = DB::table('dx_forms')->where('list_id', '=', Config::get('dx.employee_list_id'))->first()->id;

            DB::table('dx_forms_tabs')->insert([
                'form_id'=>$form_id,
                'title' => 'Languages',
                'grid_list_id' => $list_id,
                'grid_list_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'user_id')->first()->id,
                'order_index' => (DB::table('dx_forms_tabs')->where('form_id', '=', $form_id)->max('order_index')+10)
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $list = App\Libraries\DBHelper::getListByTable('dx_users_langs');
        $list_del = new Structure\StructMethod_register_delete();
        $list_del->list_id = $list->id;
        $list_del->doMethod(); 
        
        $list = App\Libraries\DBHelper::getListByTable('dx_langs');
        $list_del = new Structure\StructMethod_register_delete();
        $list_del->list_id = $list->id;
        $list_del->doMethod(); 
        
        $list = App\Libraries\DBHelper::getListByTable('dx_lang_levels');
        $list_del = new Structure\StructMethod_register_delete();
        $list_del->list_id = $list->id;
        $list_del->doMethod();   
        
        Schema::dropIfExists('dx_users_langs');
        Schema::dropIfExists('dx_langs');
        Schema::dropIfExists('dx_lang_levels');
        
        DB::table('dx_objects')->whereIn('db_name', ['dx_users_langs', 'dx_langs', 'dx_lang_levels'])->delete();
    }
}

