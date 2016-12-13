<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

use Illuminate\Support\Facades\Config;

class CreateCorporateCard extends Migration
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
        
        Schema::dropIfExists('dx_users_cards');
        Schema::dropIfExists('dx_users_cards_types');
                
        DB::table('dx_objects')->whereIn('db_name', ['dx_users_cards', 'dx_users_cards_types'])->delete();
        
        // create card types classifier
        Schema::create('dx_users_cards_types', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->string('title', 100)->nullable()->comment = "Title";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
        DB::table('dx_users_cards_types')->insert([
            ['title' => 'VISA'],
            ['title' => 'MasterCard'],
            ['title' => 'American Express'],
        ]);
        
        // create cards
        Schema::create('dx_users_cards', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->integer('user_id')->nullable()->comment = "Employee";
            
            $table->integer('card_type_id')->nullable()->unsigned()->comment = "Card type";            
            $table->string('nr', 50)->nullable()->comment = "Card number";
            
            $table->string('issuer', 250)->nullable()->comment = "Card issued by";
            $table->string('code', 20)->nullable()->comment = "Card code";
            
            $table->string('name_on_card', 250)->nullable()->comment = "Name on card";
            
            $table->date('expiry_date')->nullable()->comment = "Expiry date";
            $table->date('return_date')->nullable()->comment = "Date returned";
            
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('user_id');            
            $table->foreign('user_id')->references('id')->on('dx_users')->onDelete('cascade');
            
            $table->index('card_type_id');            
            $table->foreign('card_type_id')->references('id')->on('dx_users_cards_types');
        });
        
        // insert assets menu parent item for classifiers
        if ($this->is_hr_ui) {
            $menu_parent_id = DB::table('dx_menu')->insertGetId(['parent_id' => 252, 'title'=>'Assets', 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', 252)->max('order_index')+10), 'group_id'=>1, 'position_id' => 1]);
        }
        
         // create card types classifier register
        $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => 'dx_users_cards_types', 'title' => 'Card types' , 'is_history_logic' => 1]);
        $list_gen = new Structure\StructMethod_register_generate();
        $list_gen->obj_id = $obj_id;
        $list_gen->register_title = "Card types";
        $list_gen->form_title = "Card type";
        $list_gen->doMethod();
        
        // get list
        $list_id = App\Libraries\DBHelper::getListByTable('dx_users_cards_types')->id;       
        
        // rights
        DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins
        if ($this->is_hr_role) {
            DB::table('dx_roles_lists')->insert(['role_id' => 39, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); //HR
        }
        
        // menu
        if ($this->is_hr_ui) {
            DB::table('dx_menu')->insertGetId(['parent_id' => $menu_parent_id, 'title'=>'Card types', 'list_id'=>$list_id, 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', $menu_parent_id)->max('order_index')+10), 'group_id'=>1, 'position_id' => 1]);
        }
        
        // create cards register
        $obj_id = DB::table('dx_objects')->insertGetId(['db_name' => 'dx_users_cards', 'title' => 'Corporate cards' , 'is_history_logic' => 1]);
        $list_gen = new Structure\StructMethod_register_generate();
        $list_gen->obj_id = $obj_id;
        $list_gen->register_title = "Corporate cards";
        $list_gen->form_title = "Corporate card";
        $list_gen->doMethod();
        
        // get list
        $list_id = App\Libraries\DBHelper::getListByTable('dx_users_cards')->id;       
        
        // rights
        DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins
        if ($this->is_hr_role) {
            DB::table('dx_roles_lists')->insert(['role_id' => 39, 'list_id' => $list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); //HR
        }
        
        // menu
        if ($this->is_hr_ui) {
            $rep_menu = DB::table('dx_menu')->where('title', '=', 'Reports')->first();
            if ($rep_menu) {
                DB::table('dx_menu')->insertGetId(['parent_id' => $rep_menu->id, 'title'=>'Corporate cards', 'list_id'=>$list_id, 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', $rep_menu->id)->max('order_index')+10), 'group_id'=>1, 'position_id' => 1]);
            }
        }
        
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

        // adjust form fields
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'card_type_id')
                                            ->first()->id)
                ->update(['row_type_id' => 2]);
        
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'nr')
                                            ->first()->id)
                ->update(['row_type_id' => 2]);
        
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'expiry_date')
                                            ->first()->id)
                ->update(['row_type_id' => 2]);
        
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'issuer')
                                            ->first()->id)
                ->update(['row_type_id' => 2]);
        
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'code')
                                            ->first()->id)
                ->update(['row_type_id' => 2]);
        
        DB::table('dx_forms_fields')
                ->where('list_id', '=', $list_id)
                ->where('field_id', '=', DB::table('dx_lists_fields')
                                            ->where('list_id', '=', $list_id)
                                            ->where('db_name','=', 'return_date')
                                            ->first()->id)
                ->update(['row_type_id' => 2]);
        
        if ($this->is_hr_ui) {
            // make tab in employee profile form
            $form_id = DB::table('dx_forms')->where('list_id', '=', Config::get('dx.employee_list_id'))->first()->id;

            DB::table('dx_forms_tabs')->insert([
                'form_id'=>$form_id,
                'title' => 'Corporate cards',
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
        $list = App\Libraries\DBHelper::getListByTable('dx_users_cards');
        $list_del = new Structure\StructMethod_register_delete();
        $list_del->list_id = $list->id;
        $list_del->doMethod(); 
        
        $list = App\Libraries\DBHelper::getListByTable('dx_users_cards_types');
        $list_del = new Structure\StructMethod_register_delete();
        $list_del->list_id = $list->id;
        $list_del->doMethod();
        
        Schema::dropIfExists('dx_users_cards');
        Schema::dropIfExists('dx_users_cards_types');
                
        DB::table('dx_objects')->whereIn('db_name', ['dx_users_cards', 'dx_users_cards_types'])->delete();
        
        DB::table('dx_menu')->where('parent_id', '=', 252)->where('title', '=', 'Assets')->delete();
    }
}
