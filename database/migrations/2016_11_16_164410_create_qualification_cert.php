<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;
use Illuminate\Support\Facades\Config;

class CreateQualificationCert extends Migration
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
        
        Schema::dropIfExists('dx_users_cert');
                
        DB::table('dx_objects')->whereIn('db_name', ['dx_users_cert'])->delete();
        
        Schema::create('dx_users_cert', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->integer('user_id')->nullable()->comment = "Employee";
            $table->string('course_name', 250)->nullable()->comment = "Course name";
            $table->string('course_level', 50)->nullable()->comment = "Course level";
            $table->string('course_offered_by', 250)->nullable()->comment = "Course offered by";
            $table->string('certification_name', 500)->nullable()->comment = "Certification name";
            $table->string('file_name', 250)->nullable()->comment = "File";
            $table->string('file_guid', 100)->nullable()->comment = "File GUID";
            $table->date('valid_till')->nullable()->comment = "Valid till";           
                        
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('user_id');            
            $table->foreign('user_id')->references('id')->on('dx_users')->onDelete('cascade');                       
        });
                        
        // create employees/links register        
        $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => 'dx_users_cert', 'title' => 'Employee certificates' , 'is_history_logic' => 1]);
        $list_gen = new Structure\StructMethod_register_generate();
        $list_gen->obj_id = $obj_id;
        $list_gen->register_title = "Employee certificates";
        $list_gen->form_title = "Certificate";
        $list_gen->doMethod();
                        
        // get list
        $list_id = App\Libraries\DBHelper::getListByTable('dx_users_cert')->id;       
        
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
                'title' => 'Certificates',
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
        $list = App\Libraries\DBHelper::getListByTable('dx_users_cert');
        $list_del = new Structure\StructMethod_register_delete();
        $list_del->list_id = $list->id;
        $list_del->doMethod(); 
        
        Schema::dropIfExists('dx_users_cert');
        
        DB::table('dx_objects')->whereIn('db_name', ['dx_users_cert'])->delete();
    }
}

