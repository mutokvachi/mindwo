<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;
use Illuminate\Support\Facades\Config;

class CreateQualificationDocs extends Migration
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
        
        Schema::dropIfExists('dx_users_qual_docs');
        Schema::dropIfExists('dx_qual_doc_types');
        
        DB::table('dx_objects')->whereIn('db_name', ['dx_users_qual_docs', 'dx_qual_doc_types'])->delete();
        
        Schema::create('dx_qual_doc_types', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->string('title', 100)->nullable()->comment = "Title";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
        DB::table('dx_qual_doc_types')->insert(['title' => 'CV']);
        DB::table('dx_qual_doc_types')->insert(['title' => 'Recommendation']);
        DB::table('dx_qual_doc_types')->insert(['title' => 'Letter of motivation']);
        DB::table('dx_qual_doc_types')->insert(['title' => 'Other']);
        
        Schema::create('dx_users_qual_docs', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->integer('user_id')->nullable()->comment = "Employee";
            $table->integer('qual_doc_type_id')->nullable()->unsigned()->comment = "Document type";
            $table->string('file_name', 250)->nullable()->comment = "File";
            $table->string('file_guid', 100)->nullable()->comment = "File GUID";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('user_id');            
            $table->foreign('user_id')->references('id')->on('dx_users')->onDelete('cascade');
            
            $table->index('qual_doc_type_id');  
            $table->foreign('qual_doc_type_id')->references('id')->on('dx_qual_doc_types');            
        });         
        
        // create languages register        
        $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => 'dx_qual_doc_types', 'title' => 'Qualification document types' , 'is_history_logic' => 1]);
        $list_gen = new Structure\StructMethod_register_generate();
        $list_gen->obj_id = $obj_id;
        $list_gen->register_title = "Qualification document types";
        $list_gen->form_title = "Qualification document type";
        $list_gen->doMethod();
        
        // get list
        $list_id = App\Libraries\DBHelper::getListByTable('dx_qual_doc_types')->id;       
        
        // rights
        DB::table('dx_roles_lists')->insert(['role_id' => 39, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); //HR
        DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins

        // menu
        if ($this->is_hr_role) {
            DB::table('dx_menu')->insert(['parent_id' => 252, 'title'=>'Qualif. doc. types', 'list_id'=>$list_id, 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', 252)->max('order_index')+10), 'group_id'=>1, 'position_id' => 1]);
        }
        
        // create employees/links register        
        $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => 'dx_users_qual_docs', 'title' => 'Employee qualification documents' , 'is_history_logic' => 1]);
        $list_gen = new Structure\StructMethod_register_generate();
        $list_gen->obj_id = $obj_id;
        $list_gen->register_title = "Employee qualification documents";
        $list_gen->form_title = "Qualification document";
        $list_gen->doMethod();
                        
        // get list
        $list_id = App\Libraries\DBHelper::getListByTable('dx_users_qual_docs')->id;       
        
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
        
        //fix file field
        DB::table('dx_lists_fields')
                ->where('list_id', '=', $list_id)
                ->where('db_name', '=', 'file_guid')
                ->delete();
        
        DB::table('dx_lists_fields')
                ->where('list_id', '=', $list_id)
                ->where('db_name', '=', 'file_name')
                ->update([
                    'type_id'=>  \App\Libraries\DBHelper::FIELD_TYPE_FILE
                ]);
                
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
                'title' => 'CVs & other',
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
        $list = App\Libraries\DBHelper::getListByTable('dx_users_qual_docs');
        $list_del = new Structure\StructMethod_register_delete();
        $list_del->list_id = $list->id;
        $list_del->doMethod(); 
        
        $list = App\Libraries\DBHelper::getListByTable('dx_qual_doc_types');
        $list_del = new Structure\StructMethod_register_delete();
        $list_del->list_id = $list->id;
        $list_del->doMethod();         
        
        
        Schema::dropIfExists('dx_users_qual_docs');
        Schema::dropIfExists('dx_qual_doc_types');
        
        DB::table('dx_objects')->whereIn('db_name', ['dx_users_qual_docs', 'dx_qual_doc_types'])->delete();
    }
}

