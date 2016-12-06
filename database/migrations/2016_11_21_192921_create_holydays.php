<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

use Illuminate\Support\Facades\Config;
use App;

class CreateHolydays extends Migration
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
        
        Schema::dropIfExists('dx_holidays');
        Schema::dropIfExists('dx_month_days');
        Schema::dropIfExists('dx_months');
                
        DB::table('dx_objects')->whereIn('db_name', ['dx_month_days', 'dx_months', 'dx_holidays'])->delete();
        
        // create month days classifier
        Schema::create('dx_month_days', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->string('title', 100)->nullable()->comment = "Title";
            $table->string('code', 20)->nullable()->comment = "Code";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
        // fill months days with values
        DB::table('dx_month_days')->insert(['title' => '01', 'code' => '1']);
        DB::table('dx_month_days')->insert(['title' => '02', 'code' => '2']);
        DB::table('dx_month_days')->insert(['title' => '03', 'code' => '3']);
        
        for ($i=4; $i<21; $i++) {
            DB::table('dx_month_days')->insert(['title' => sprintf('%02d',$i), 'code' => $i]);
        }
        
        DB::table('dx_month_days')->insert(['title' => '21', 'code' => '21']);
        DB::table('dx_month_days')->insert(['title' => '22', 'code' => '22']);
        DB::table('dx_month_days')->insert(['title' => '23', 'code' => '23']);
        
        for ($i=24; $i<29; $i++) {
            DB::table('dx_month_days')->insert(['title' => $i, 'code' => $i]);
        }
        
        DB::table('dx_month_days')->insert(['title' => 'Last day', 'code' => 'LAST']);
        
        // create months classifiers
        Schema::create('dx_months', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->string('title', 100)->nullable()->comment = "Title";
            $table->integer('nr')->nullable()->unsigned()->comment = "Nr.";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
        // fill months with values
        $month_arr = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        foreach ($month_arr as $key => $m) {
            DB::table('dx_months')->insert(['title' => sprintf('%02d',$key+1) . ' - ' . $m, 'nr' => $key+1]);
        }
        
        // create holidays
        Schema::create('dx_holidays', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->string('title', 100)->nullable()->comment = "Title";
            $table->integer('from_month_id')->nullable()->unsigned()->comment = "From month";
            $table->integer('from_day_id')->nullable()->unsigned()->comment = "From day";
            
            $table->boolean('is_several_days')->nullable()->default(false)->comment = "Is several days";
            
            $table->integer('to_month_id')->nullable()->unsigned()->comment = "To month";
            $table->integer('to_day_id')->nullable()->unsigned()->comment = "To day";
            $table->integer('country_id')->nullable()->unsigned()->comment = "Country";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('from_month_id');            
            $table->foreign('from_month_id')->references('id')->on('dx_months');
            
            $table->index('from_day_id');            
            $table->foreign('from_day_id')->references('id')->on('dx_month_days');
            
            $table->index('to_month_id');            
            $table->foreign('to_month_id')->references('id')->on('dx_months');
            
            $table->index('to_day_id');            
            $table->foreign('to_day_id')->references('id')->on('dx_month_days');
            
            $table->index('country_id');            
            $table->foreign('country_id')->references('id')->on('dx_countries');
        });
        
        DB::table('dx_holidays')->insert(["title" => "New Year's Day", 'from_month_id' => 1, 'from_day_id' => 1]);
        DB::table('dx_holidays')->insert(["title" => "Christmas Day", 'from_month_id' => 12, 'from_day_id' => 25]);
        
        // insert calendar menu parent item for classifiers
        if ($this->is_hr_ui) {
            $menu_parent_id = DB::table('dx_menu')->insertGetId(['parent_id' => 252, 'title'=>'Calendar', 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', 252)->max('order_index')+10), 'group_id'=>1, 'position_id' => 1]);
        }
        
        // create months classifier register
        $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => 'dx_months', 'title' => 'Months' , 'is_history_logic' => 1]);
        $list_gen = new Structure\StructMethod_register_generate();
        $list_gen->obj_id = $obj_id;
        $list_gen->register_title = "Months";
        $list_gen->form_title = "Month";
        $list_gen->doMethod();
        
        // get list
        $list_id = App\Libraries\DBHelper::getListByTable('dx_months')->id;       
        
        // rights
        DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins

        // menu
        if ($this->is_hr_ui) {
            DB::table('dx_menu')->insertGetId(['parent_id' => $menu_parent_id, 'title'=>'Months', 'list_id'=>$list_id, 'order_index' => 10, 'group_id'=>1, 'position_id' => 1]);
        }
        // create months days classifier register
        $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => 'dx_month_days', 'title' => 'Month days' , 'is_history_logic' => 1]);
        $list_gen = new Structure\StructMethod_register_generate();
        $list_gen->obj_id = $obj_id;
        $list_gen->register_title = "Month days";
        $list_gen->form_title = "Month day";
        $list_gen->doMethod();
        
        // get list
        $list_id = App\Libraries\DBHelper::getListByTable('dx_month_days')->id;       
        
        // rights
        DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins
        
        // menu
        if ($this->is_hr_ui) {
            DB::table('dx_menu')->insertGetId(['parent_id' => $menu_parent_id, 'title'=>'Month days', 'list_id'=>$list_id, 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', $menu_parent_id)->max('order_index')+10), 'group_id'=>1, 'position_id' => 1]);
        }
        
        // create holidays register
        $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => 'dx_holidays', 'title' => 'Holidays' , 'is_history_logic' => 1]);
        $list_gen = new Structure\StructMethod_register_generate();
        $list_gen->obj_id = $obj_id;
        $list_gen->register_title = "Holidays";
        $list_gen->form_title = "Holiday";
        $list_gen->doMethod();
        
        // get list
        $list_id = App\Libraries\DBHelper::getListByTable('dx_holidays')->id;       
        
        // rights
        DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins
        DB::table('dx_roles_lists')->insert(['role_id' => 39, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); //HR

        // menu
        if ($this->is_hr_ui) {
            DB::table('dx_menu')->insertGetId(['parent_id' => $menu_parent_id, 'title'=>'Holidays', 'list_id'=>$list_id, 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', $menu_parent_id)->max('order_index')+10), 'group_id'=>1, 'position_id' => 1]);
        }
        
        // adjust form fields
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'from_month_id')
                                            ->first()->id)
                ->update(['row_type_id' => 2]);
        
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'from_day_id')
                                            ->first()->id)
                ->update(['row_type_id' => 2]);
        
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'to_month_id')
                                            ->first()->id)
                ->update(['row_type_id' => 2]);
        
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'to_day_id')
                                            ->first()->id)
                ->update(['row_type_id' => 2]);
        
        // add special JavaScript
        $form_id = DB::table('dx_forms')->where('list_id', '=', $list_id)->first()->id;
        
        $dir = storage_path() . DIRECTORY_SEPARATOR . "app" . DIRECTORY_SEPARATOR . "updates" . DIRECTORY_SEPARATOR;       
        $file_js = $dir . '2016_11_21_holidays_form.js';
        $content = File::get($file_js);

        DB::table('dx_forms_js')->insert([
            'title' => 'Show or hide interval end fields acording to option "Is several days"',
            'form_id' => $form_id,
            'js_code' => $content
        ]);
      
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $list = App\Libraries\DBHelper::getListByTable('dx_holidays');
        $list_del = new Structure\StructMethod_register_delete();
        $list_del->list_id = $list->id;
        $list_del->doMethod(); 
        
        $list = App\Libraries\DBHelper::getListByTable('dx_month_days');
        $list_del = new Structure\StructMethod_register_delete();
        $list_del->list_id = $list->id;
        $list_del->doMethod(); 
        
        $list = App\Libraries\DBHelper::getListByTable('dx_months');
        $list_del = new Structure\StructMethod_register_delete();
        $list_del->list_id = $list->id;
        $list_del->doMethod();  
        
        Schema::dropIfExists('dx_holidays');
        Schema::dropIfExists('dx_month_days');
        Schema::dropIfExists('dx_months');
                
        DB::table('dx_objects')->whereIn('db_name', ['dx_month_days', 'dx_months', 'dx_holidays'])->delete();
        
        DB::table('dx_menu')->where('parent_id', '=', 63)->where('title', '=', 'Calendar')->delete();
    }
}
