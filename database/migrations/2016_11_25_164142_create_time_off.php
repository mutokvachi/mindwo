<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

class CreateTimeOff extends Migration
{
    
    private $menu_parent_id = 0;
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {     
        
        $this->fixFormsJSCascade();
        
        $this->deleteTables();
        
        $this->menu_parent_id = DB::table('dx_menu')->insertGetId(['parent_id' => 252, 'title'=>'Time off', 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', 252)->max('order_index')+10), 'group_id'=>1, 'position_id' => 1]);
        
        // classifiers
        $this->createAccrualStart();
        $this->createTimeOffTypes();        
        $this->createCarryOverDates();
        $this->createAccrualTypes();
        $this->createCarryOverTypes();
        $this->createTimeoffRecords();
        
        $this->createAccrualPolicies();
        $this->createAccrualLevels();
        $this->createTimeoffCalc();
        $this->createUsersAccrualPolicies();
        
        // hide ID fields in all forms
        \App\Libraries\DBHelper::removeFieldsFromAllForms('dx_accrual_levels', ['id'], true);
        \App\Libraries\DBHelper::removeFieldsFromAllForms('dx_accrual_policies', ['id'], true);
        \App\Libraries\DBHelper::removeFieldsFromAllForms('dx_timeoff_types', ['id'], true);
        \App\Libraries\DBHelper::removeFieldsFromAllForms('dx_timeoff_calc', ['id'], true);
        \App\Libraries\DBHelper::removeFieldsFromAllForms('dx_users_accrual_policies', ['id'], true);
        
    }
    
    private function fixFormsJSCascade() {
        // delete unrelated rows
        $js_rows = DB::table('dx_forms_js as js')
              ->select('js.id')
              ->leftJoin('dx_forms as f', 'js.form_id', '=', 'f.id')
              ->whereNull('f.id')
              ->get();
                
        Schema::disableForeignKeyConstraints();
        foreach($js_rows as $js) {
            DB::table('dx_forms_js')->where('id','=',$js->id)->delete();
        }
        Schema::enableForeignKeyConstraints();
        
        Schema::table('dx_forms_js', function (Blueprint $table) {                       
            $table->foreign('form_id')->references('id')->on('dx_forms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \App\Libraries\DBHelper::deleteRegister('dx_users_accrual_policies');
        \App\Libraries\DBHelper::deleteRegister('dx_timeoff_calc');
        \App\Libraries\DBHelper::deleteRegister('dx_accrual_levels');
        \App\Libraries\DBHelper::deleteRegister('dx_accrual_policies');
        \App\Libraries\DBHelper::deleteRegister('dx_timeoff_types');
        \App\Libraries\DBHelper::deleteRegister('dx_carryover_dates');
        \App\Libraries\DBHelper::deleteRegister('dx_accrual_types');
        \App\Libraries\DBHelper::deleteRegister('dx_carryover_types');
        \App\Libraries\DBHelper::deleteRegister('dx_accrual_start_types'); 
        \App\Libraries\DBHelper::deleteRegister('dx_timeoff_records_types'); 
                
        $this->deleteTables();
        
        DB::table('dx_menu')->where('parent_id', '=', 252)->where('title', '=', 'Time off')->delete();
        
        Schema::table('dx_forms_js', function (Blueprint $table) {
            $table->dropForeign(['form_id']);
        });
    }
    
    private function deleteTables() {
        
        Schema::dropIfExists('dx_users_accrual_policies');
        Schema::dropIfExists('dx_timeoff_calc');
        Schema::dropIfExists('dx_accrual_levels');
        Schema::dropIfExists('dx_accrual_policies');
        Schema::dropIfExists('dx_timeoff_types');
        Schema::dropIfExists('dx_carryover_dates');
        Schema::dropIfExists('dx_carryover_types');
        Schema::dropIfExists('dx_accrual_types');
        Schema::dropIfExists('dx_accrual_start_types');
        Schema::dropIfExists('dx_timeoff_records_types');
        
        DB::table('dx_objects')->whereIn('db_name', ['dx_timeoff_records_types', 'dx_accrual_start_types', 'dx_accrual_types', 'dx_carryover_types', 'dx_carryover_dates', 'dx_timeoff_types', 'dx_accrual_policies', 'dx_accrual_levels', 'dx_timeoff_calc', 'dx_users_accrual_policies'])->delete();
    }
    
    private function createTimeOffTypes() {        
        
        $table_name = "dx_timeoff_types";
        $list_name = "Time off types";
        $item_name = "Time off type";
        
        // create table
        Schema::create($table_name, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->string('title', 100)->comment = "Title";
            $table->string('icon', 100)->nullable()->comment = "Icon";
            $table->string('color', 20)->nullable()->comment = "Color";
            $table->boolean('is_accrual_hours')->nullable()->default(false)->comment = "Display accrued amount as hours";
            $table->boolean('is_disabled')->nullable()->default(false)->comment = "Is disabled";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
        // insert initial values
        DB::table($table_name)->insert([
            ['title' => 'Vacation', 'icon' => 'fa fa-sun-o', 'color' => 'rgba(102,214,157,1)'],
            ['title' => 'Sick', 'icon' => 'fa fa-medkit', 'color' => 'rgba(236,89,89,1)'],
        ]);
        
        // create register
        $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => $table_name, 'title' => $list_name , 'is_history_logic' => 1]);
        $list_gen = new Structure\StructMethod_register_generate();
        $list_gen->obj_id = $obj_id;
        $list_gen->register_title = $list_name;
        $list_gen->form_title = $item_name;
        $list_gen->doMethod();
        
        // get list
        $list_id = App\Libraries\DBHelper::getListByTable($table_name)->id;       
        
        // adjust form fields
         DB::table('dx_lists_fields')
            ->where('list_id', '=', $list_id)
            ->where('db_name','=', 'color')                                            
            ->update(['type_id' => \App\Libraries\DBHelper::FIELD_TYPE_COLOR]);
         
        // adjust fields in form
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'icon')
                                            ->first()->id)
                ->update(['row_type_id' => 2]);
        
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'color')
                                            ->first()->id)
                ->update(['row_type_id' => 2]);
        
        // set rights
        DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins
        DB::table('dx_roles_lists')->insert(['role_id' => 39, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); //HR

        // create menu
        DB::table('dx_menu')->insertGetId(['parent_id' => $this->menu_parent_id, 'title'=>$list_name, 'list_id'=>$list_id, 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', $this->menu_parent_id)->max('order_index')+10), 'group_id'=>1, 'position_id' => 1]);

    }
    
    private function createCarryOverDates() {        
        
        $table_name = "dx_carryover_dates";
        $list_name = "Carryover dates";
        $item_name = "Carryover date";
        
        // create table
        Schema::create($table_name, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->string('title', 100)->comment = "Title";
            $table->string('code', 20)->nullable()->comment = "Code";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
        // insert initial values
        DB::table($table_name)->insert([
            ['title' => '1st January', 'code' => 'JANUARY1'],
            ['title' => 'Hire date', 'code' => 'HIREDATE'],
            ['title' => 'Other', 'code' => 'OTHER'],
        ]);
        
        // create register
        $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => $table_name, 'title' => $list_name , 'is_history_logic' => 1]);
        $list_gen = new Structure\StructMethod_register_generate();
        $list_gen->obj_id = $obj_id;
        $list_gen->register_title = $list_name;
        $list_gen->form_title = $item_name;
        $list_gen->doMethod();
        
        // get list
        $list_id = App\Libraries\DBHelper::getListByTable($table_name)->id;       
                
        // set rights
        DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins
 
        // create menu
        DB::table('dx_menu')->insertGetId(['parent_id' => $this->menu_parent_id, 'title'=>$list_name, 'list_id'=>$list_id, 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', $this->menu_parent_id)->max('order_index')+10), 'group_id'=>1, 'position_id' => 1]);

    }
    
    private function createAccrualStart() {        
        
        $table_name = "dx_accrual_start_types";
        $list_name = "Accrual start types";
        $item_name = "Accrual start type";
        
        // create table
        Schema::create($table_name, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->string('title', 100)->comment = "Title";
            $table->string('code', 20)->nullable()->comment = "Code";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
        // insert initial values
        DB::table($table_name)->insert([
            ['title' => 'Days after hire date', 'code' => 'DAY'],
            ['title' => 'Weeks after hire date', 'code' => 'WEEK'],
            ['title' => 'Months after hire date', 'code' => 'Month'],
            ['title' => 'Years after hire date', 'code' => 'Year'],
        ]);
        
        // create register
        $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => $table_name, 'title' => $list_name , 'is_history_logic' => 1]);
        $list_gen = new Structure\StructMethod_register_generate();
        $list_gen->obj_id = $obj_id;
        $list_gen->register_title = $list_name;
        $list_gen->form_title = $item_name;
        $list_gen->doMethod();
        
        // get list
        $list_id = App\Libraries\DBHelper::getListByTable($table_name)->id;       
                
        // set rights
        DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins
 
        // create menu
        DB::table('dx_menu')->insertGetId(['parent_id' => $this->menu_parent_id, 'title'=>$list_name, 'list_id'=>$list_id, 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', $this->menu_parent_id)->max('order_index')+10), 'group_id'=>1, 'position_id' => 1]);

    }
    
    private function createCarryOverTypes() {        
        
        $table_name = "dx_carryover_types";
        $list_name = "Carryover types";
        $item_name = "Carryover type";
        
        // create table
        Schema::create($table_name, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->string('title', 100)->comment = "Title";            
            $table->string('code', 20)->nullable()->comment = "Code";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
        // insert initial values
        DB::table($table_name)->insert([
            ['title' => 'None - use it or loose it', 'code' => 'NONE'],
            ['title' => 'Up to...', 'code' => 'UPTO'],
            ['title' => 'Unlimited', 'code' => 'UNLIMITED'],
        ]);
        
        // create register
        $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => $table_name, 'title' => $list_name , 'is_history_logic' => 1]);
        $list_gen = new Structure\StructMethod_register_generate();
        $list_gen->obj_id = $obj_id;
        $list_gen->register_title = $list_name;
        $list_gen->form_title = $item_name;
        $list_gen->doMethod();
        
        // get list
        $list_id = App\Libraries\DBHelper::getListByTable($table_name)->id;       
                
        // set rights
        DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins

        // create menu
        DB::table('dx_menu')->insertGetId(['parent_id' => $this->menu_parent_id, 'title'=>$list_name, 'list_id'=>$list_id, 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', $this->menu_parent_id)->max('order_index')+10), 'group_id'=>1, 'position_id' => 1]);

    }
    
    private function createAccrualTypes() {        
        
        $table_name = "dx_accrual_types";
        $list_name = "Accrual types";
        $item_name = "Accrual type";
        
        // create table
        Schema::create($table_name, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->string('title', 100)->comment = "Title";
            $table->string('code', 20)->nullable()->comment = "Code";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
        // insert initial values
        DB::table($table_name)->insert([
            ['title' => 'Daily', 'code' => 'DAILY'],
            ['title' => 'Monthly', 'code' => 'MONTHLY'],
            ['title' => 'Anniversary', 'code' => 'ANNIVERSARY'],
        ]);
        
        // create register
        $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => $table_name, 'title' => $list_name , 'is_history_logic' => 1]);
        $list_gen = new Structure\StructMethod_register_generate();
        $list_gen->obj_id = $obj_id;
        $list_gen->register_title = $list_name;
        $list_gen->form_title = $item_name;
        $list_gen->doMethod();
        
        // get list
        $list_id = App\Libraries\DBHelper::getListByTable($table_name)->id;       
                
        // set rights
        DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins

        // create menu
        DB::table('dx_menu')->insertGetId(['parent_id' => $this->menu_parent_id, 'title'=>$list_name, 'list_id'=>$list_id, 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', $this->menu_parent_id)->max('order_index')+10), 'group_id'=>1, 'position_id' => 1]);

    }
    
    private function createAccrualPolicies() {        
        
        $table_name = "dx_accrual_policies";
        $list_name = "Accrual policies";
        $item_name = "Accrual policy";
        
        // create table
        Schema::create($table_name, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->integer('timeoff_type_id')->unsigned()->comment = "Time off type";
            $table->string('title', 100)->comment = "Title";            
            $table->integer('carryover_date_id')->default(1)->unsigned()->comment = "Carryover date";
            $table->integer('month_day_id')->default(1)->unsigned()->comment = "Day";
            $table->integer('month_id')->default(1)->unsigned()->comment = "Month";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('timeoff_type_id');            
            $table->foreign('timeoff_type_id')->references('id')->on('dx_timeoff_types');
            
            $table->index('carryover_date_id');            
            $table->foreign('carryover_date_id')->references('id')->on('dx_carryover_dates');
            
            $table->index('month_day_id');            
            $table->foreign('month_day_id')->references('id')->on('dx_month_days');
            
            $table->index('month_id');            
            $table->foreign('month_id')->references('id')->on('dx_months');
        });
        
        // create register
        $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => $table_name, 'title' => $list_name , 'is_history_logic' => 1]);
        $list_gen = new Structure\StructMethod_register_generate();
        $list_gen->obj_id = $obj_id;
        $list_gen->register_title = $list_name;
        $list_gen->form_title = $item_name;
        $list_gen->doMethod();
        
        // get list
        $list_id = App\Libraries\DBHelper::getListByTable($table_name)->id;
        
        // reorganize view fields - hide or remove unneeded
        \App\Libraries\DBHelper::removeFieldsFromAllViews($table_name, ['id'], true); // hide ID field
        \App\Libraries\DBHelper::removeFieldsFromAllViews($table_name, ['month_day_id', 'month_id'], false); // remove day and month from view
        
        // set rights
        DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins
        DB::table('dx_roles_lists')->insert(['role_id' => 39, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); //HR

        // create menu
        DB::table('dx_menu')->insertGetId(['parent_id' => $this->menu_parent_id, 'title'=>$list_name, 'list_id'=>$list_id, 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', $this->menu_parent_id)->max('order_index')+10), 'group_id'=>1, 'position_id' => 1]);

        // tab in related form
        $timeoff_list_id = App\Libraries\DBHelper::getListByTable('dx_timeoff_types')->id; 
        $form_id = DB::table('dx_forms')->where('list_id', '=', $timeoff_list_id)->first()->id;
        
        DB::table('dx_forms_tabs')->insert([
            'form_id'=>$form_id,
            'title' => 'Accrual policies',
            'grid_list_id' => $list_id,
            'grid_list_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'timeoff_type_id')->first()->id,
            'order_index' => 10
        ]);
        
        // adjust fields in form
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'month_day_id')
                                            ->first()->id)
                ->update(['row_type_id' => 2]);
        
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'month_id')
                                            ->first()->id)
                ->update(['row_type_id' => 2]);
        
        // add special JavaScript
        \App\Libraries\DBHelper::addJavaScriptToForm($table_name, '2016_11_27_accrual_policy_days.js', 'Show or hide day and month fields accroding to selected carryover day value');

    }
    
    private function createAccrualLevels() {        
        
        $table_name = "dx_accrual_levels";
        $list_name = "Accrual levels";
        $item_name = "Accrual level";
        
        // create table
        Schema::create($table_name, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->integer('accrual_policy_id')->unsigned()->comment = "Accrual policy";
            
            $table->integer('start_moment')->unsigned()->default(1)->comment = "Starts";
            $table->integer('start_type_id')->unsigned()->default(1)->comment = "Period type";
            $table->decimal('accrued_amount', 5, 2)->default(1)->unsigned()->comment = "Amount accrued (hours)";
            $table->integer('accrual_type_id')->default(1)->unsigned()->comment = "Accryal period";
            $table->integer('month_day_id')->default(1)->unsigned()->comment = "Day of month";
            $table->decimal('max_accrual', 5, 2)->nullable()->unsigned()->comment = "Max accrual (hours)";
            $table->integer('carryover_type_id')->default(1)->unsigned()->comment = "Carryover type";
            $table->integer('carryover_max')->nullable()->unsigned()->comment = "Max carryover amount";
                        
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('accrual_policy_id');            
            $table->foreign('accrual_policy_id')->references('id')->on('dx_accrual_policies');
            
            $table->index('start_type_id');            
            $table->foreign('start_type_id')->references('id')->on('dx_accrual_start_types');
            
            $table->index('accrual_type_id');            
            $table->foreign('accrual_type_id')->references('id')->on('dx_accrual_types');
            
            $table->index('month_day_id');            
            $table->foreign('month_day_id')->references('id')->on('dx_month_days');
            
            $table->index('carryover_type_id');            
            $table->foreign('carryover_type_id')->references('id')->on('dx_carryover_types');
        });
        
        // create register
        $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => $table_name, 'title' => $list_name , 'is_history_logic' => 1]);
        $list_gen = new Structure\StructMethod_register_generate();
        $list_gen->obj_id = $obj_id;
        $list_gen->register_title = $list_name;
        $list_gen->form_title = $item_name;
        $list_gen->doMethod();
        
        // get list
        $list_id = App\Libraries\DBHelper::getListByTable($table_name)->id;       

        // reorganize view fields - hide or remove unneeded
        \App\Libraries\DBHelper::removeFieldsFromAllViews($table_name, ['id'], true); // hide ID field
        \App\Libraries\DBHelper::removeFieldsFromAllViews($table_name, ['month_day_id', 'max_accrual', 'carryover_max'], false);
        
        // set rights
        DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins
        DB::table('dx_roles_lists')->insert(['role_id' => 39, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); //HR

        // adjust fields in form
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'start_moment')
                                            ->first()->id)
                ->update(['row_type_id' => 2]);
        
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'start_type_id')
                                            ->first()->id)
                ->update(['row_type_id' => 2]);
        
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'accrued_amount')
                                            ->first()->id)
                ->update(['row_type_id' => 3]);
        
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'accrual_type_id')
                                            ->first()->id)
                ->update(['row_type_id' => 3]);
        
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'month_day_id')
                                            ->first()->id)
                ->update(['row_type_id' => 3]);
        
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'carryover_type_id')
                                            ->first()->id)
                ->update(['row_type_id' => 2]);
        
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'carryover_max')
                                            ->first()->id)
                ->update(['row_type_id' => 2]);
        
        // tab in related form
        $policy_list_id = App\Libraries\DBHelper::getListByTable('dx_accrual_policies')->id; 
        $form_id = DB::table('dx_forms')->where('list_id', '=', $policy_list_id)->first()->id;
        
        DB::table('dx_forms_tabs')->insert([
            'form_id'=>$form_id,
            'title' => 'Accrual levels',
            'grid_list_id' => $list_id,
            'grid_list_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'accrual_policy_id')->first()->id,
            'order_index' => 10
        ]);
        
        // add special JavaScript
        \App\Libraries\DBHelper::addJavaScriptToForm($table_name, '2016_11_25_accrual_level_month_day.js', 'Show or hide month day field accroding to selected accrual type value');
        \App\Libraries\DBHelper::addJavaScriptToForm($table_name, '2016_11_25_accrual_level_up_to.js', 'Show or hide max carryover amoount field accroding to selected caryover type value');
    }
    
    private function createTimeoffCalc() {        
        
        $table_name = "dx_timeoff_calc";
        $list_name = "Time off balance";
        $item_name = "Time off item";
        
        // create table
        Schema::create($table_name, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->integer('user_id')->comment = "Employee";
            $table->integer('timeoff_type_id')->unsigned()->comment = "Time off type";
            $table->integer('record_type_id')->unsigned()->default(1)->comment = "Record type"; // 1 - accrued; 2 - loss due to carry over limit; 3 - used time off (leave request); 4 - manually entered
            $table->date('calc_date')->comment = "Date";
            $table->decimal('amount', 7, 3)->default(0)->comment = "Time amount (hours)";
            $table->decimal('balance', 7, 3)->default(0)->comment = "Balance (hours)";
            $table->date('from_date')->nullable()->comment = "Period from";
            $table->date('to_date')->nullable()->comment = "Period to";
            $table->string('notes', 1000)->nullable()->comment = "Notes";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('timeoff_type_id');            
            $table->foreign('timeoff_type_id')->references('id')->on('dx_timeoff_types');
            
            $table->index('record_type_id');            
            $table->foreign('record_type_id')->references('id')->on('dx_timeoff_records_types');
            
            $table->index('user_id');            
            $table->foreign('user_id')->references('id')->on('dx_users');
        });
        
        // create register
        $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => $table_name, 'title' => $list_name , 'is_history_logic' => 1]);
        $list_gen = new Structure\StructMethod_register_generate();
        $list_gen->obj_id = $obj_id;
        $list_gen->register_title = $list_name;
        $list_gen->form_title = $item_name;
        $list_gen->doMethod();
        
        // get list
        $list_id = App\Libraries\DBHelper::getListByTable($table_name)->id;       

        // reorganize view fields - hide or remove unneeded
        \App\Libraries\DBHelper::removeFieldsFromAllViews($table_name, ['id'], true); // hide ID field
        
        // set rights
        DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins
        DB::table('dx_roles_lists')->insert(['role_id' => 39, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); //HR

        //fix user field (because we have 2 registers in 1 table dx_users)
        DB::table('dx_lists_fields')
                ->where('list_id', '=', $list_id)
                ->where('db_name', '=', 'user_id')
                ->update([
                    'rel_list_id'=>Config::get('dx.employee_list_id'),
                    'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', Config::get('dx.employee_list_id'))->where('db_name', '=', 'display_name')->first()->id,
                    'type_id' => \App\Libraries\DBHelper::FIELD_TYPE_LOOKUP
                ]);
        
        // adjust fields in form
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'timeoff_type_id')
                                            ->first()->id)
                ->update(['row_type_id' => 2]);
        
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'record_type_id')
                                            ->first()->id)
                ->update(['row_type_id' => 2]);
        
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'calc_date')
                                            ->first()->id)
                ->update(['row_type_id' => 3]);
        
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'amount')
                                            ->first()->id)
                ->update(['row_type_id' => 3]);
        
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'balance')
                                            ->first()->id)
                ->update(['row_type_id' => 3]);
        
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'from_date')
                                            ->first()->id)
                ->update(['row_type_id' => 2]);
        
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'to_date')
                                            ->first()->id)
                ->update(['row_type_id' => 2]);
        
        // menu
        $rep_menu = DB::table('dx_menu')->where('title', '=', 'Reports')->first();
        if ($rep_menu) {
            DB::table('dx_menu')->insertGetId(['parent_id' => $rep_menu->id, 'title'=>$list_name, 'list_id'=>$list_id, 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', $rep_menu->id)->max('order_index')+10), 'group_id'=>1, 'position_id' => 1]);
        }
    }
    
    private function createTimeoffRecords() {        
        
        $table_name = "dx_timeoff_records_types";
        $list_name = "Time off record types";
        $item_name = "Time off record type";
        
        // create table
        Schema::create($table_name, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->string('title', 100)->comment = "Title";
            $table->string('code', 20)->nullable()->comment = "Code";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
        // insert initial values // 1 - accrued; 2 - loss due to carry over limit; 3 - used time off (leave request); 4 - manually entered
        DB::table($table_name)->insert([
            ['title' => 'Accrued', 'code' => 'ACCRUED'],
            ['title' => 'Loss due to carry over limit', 'code' => 'LOSS'],
            ['title' => 'Leave', 'code' => 'LEAVE'],
            ['title' => 'Manually entered', 'code' => 'MANUAL'],
        ]);
        
        // create register
        $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => $table_name, 'title' => $list_name , 'is_history_logic' => 1]);
        $list_gen = new Structure\StructMethod_register_generate();
        $list_gen->obj_id = $obj_id;
        $list_gen->register_title = $list_name;
        $list_gen->form_title = $item_name;
        $list_gen->doMethod();
        
        // get list
        $list_id = App\Libraries\DBHelper::getListByTable($table_name)->id;       
                
        // set rights
        DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins

        // create menu
        DB::table('dx_menu')->insertGetId(['parent_id' => $this->menu_parent_id, 'title'=>$list_name, 'list_id'=>$list_id, 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', $this->menu_parent_id)->max('order_index')+10), 'group_id'=>1, 'position_id' => 1]);

    }
    
    private function createUsersAccrualPolicies() {        
        
        $table_name = "dx_users_accrual_policies";
        $list_name = "Employees accrual policies";
        $item_name = "Employee accrual policy";
        
        // create table
        Schema::create($table_name, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->integer('user_id')->comment = "Employee";
            $table->integer('timeoff_type_id')->unsigned()->comment = "Time off type";
            $table->integer('accrual_policy_id')->unsigned()->default(1)->comment = "Time off policy";
            $table->date('eff_date')->comment = "Effective date";
            $table->date('end_date')->nullable()->comment = "End date";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('timeoff_type_id');            
            $table->foreign('timeoff_type_id')->references('id')->on('dx_timeoff_types');
            
            $table->index('accrual_policy_id');            
            $table->foreign('accrual_policy_id')->references('id')->on('dx_accrual_policies');
            
            $table->index('user_id');            
            $table->foreign('user_id')->references('id')->on('dx_users');
        });
        
        // create register
        $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => $table_name, 'title' => $list_name , 'is_history_logic' => 1]);
        $list_gen = new Structure\StructMethod_register_generate();
        $list_gen->obj_id = $obj_id;
        $list_gen->register_title = $list_name;
        $list_gen->form_title = $item_name;
        $list_gen->doMethod();
        
        // get list
        $list_id = App\Libraries\DBHelper::getListByTable($table_name)->id;       

        // reorganize view fields - hide or remove unneeded
        \App\Libraries\DBHelper::removeFieldsFromAllViews($table_name, ['id'], true); // hide ID field
        
        // set rights
        DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins
        DB::table('dx_roles_lists')->insert(['role_id' => 39, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); //HR

        //fix user field (because we have 2 registers in 1 table dx_users)
        DB::table('dx_lists_fields')
                ->where('list_id', '=', $list_id)
                ->where('db_name', '=', 'user_id')
                ->update([
                    'rel_list_id'=>Config::get('dx.employee_list_id'),
                    'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', Config::get('dx.employee_list_id'))->where('db_name', '=', 'display_name')->first()->id,
                    'type_id' => \App\Libraries\DBHelper::FIELD_TYPE_LOOKUP
                ]);
        
        // set binded fields
        $bind_list = \App\Libraries\DBHelper::getListByTable('dx_accrual_policies');
        DB::table('dx_lists_fields')
            ->where('list_id', '=', $list_id)
            ->where('db_name','=', 'timeoff_type_id')
            ->update([
                'binded_field_id' => DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'accrual_policy_id')
                                            ->first()->id,
                'binded_rel_field_id' => DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $bind_list->id)
                                            ->where('db_name','=', 'timeoff_type_id')
                                            ->first()->id
            ]);
        
        // set default date NOW
        DB::table('dx_lists_fields')
            ->where('list_id', '=', $list_id)
            ->where('db_name','=', 'eff_date')
            ->update(['default_value' => '[NOW]']);
        
        // adjust fields in form
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'timeoff_type_id')
                                            ->first()->id)
                ->update(['row_type_id' => 1]);
        
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'accrual_policy_id')
                                            ->first()->id)
                ->update(['row_type_id' => 1]);
        
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'eff_date')
                                            ->first()->id)
                ->update(['row_type_id' => 2]);
        
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'end_date')
                                            ->first()->id)
                ->update(['row_type_id' => 2]);
        
        // menu
        $rep_menu = DB::table('dx_menu')->where('title', '=', 'Reports')->first();
        if ($rep_menu) {
            DB::table('dx_menu')->insertGetId(['parent_id' => $rep_menu->id, 'title'=>'Employees accrual policies', 'list_id'=>$list_id, 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', $rep_menu->id)->max('order_index')+10), 'group_id'=>1, 'position_id' => 1]);
        }
        
        // add special JavaScript
        \App\Libraries\DBHelper::addJavaScriptToForm($table_name, '2016_11_27_employee_timeoff.js', 'Show or hide accrual policy field accroding to selected time off type value');

    }
}
