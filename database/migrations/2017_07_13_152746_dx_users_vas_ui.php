<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

class DxUsersVasUi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!$this->isEdu()) {
            return;
        }
        
        DB::transaction(function () {
            
            // create parent menu
            $parent_menu_id = DB::table('dx_menu')->insertgetId(['title'=>'Lietotāji', 'order_index' => 15, 'fa_icon' => 'fa fa-users', 'group_id' => 1, 'position_id' => 1]);
            
            $this->createUserList([
                'list_title' => 'VAS koordinatori',
                'item_title' => 'VAS koordinators',
                'parent_menu_id' => $parent_menu_id,
                'person_code_required' => 1,
                'criteria_role_title' => 'Ir VAS koordinatora loma',
                'criteria_role_field' => 'is_role_coordin_main'
            ]);
            
            $this->createUserList([
                'list_title' => 'Iestāžu koordinatori',
                'item_title' => 'Iestādes koordinators',
                'parent_menu_id' => $parent_menu_id,
                'person_code_required' => 1,
                'criteria_role_title' => 'Ir iestādes koordinatora loma',
                'criteria_role_field' => 'is_role_coordin'
            ]);
            
            $this->createUserList([
                'list_title' => 'Pasniedzēji',
                'item_title' => 'Pasniedzējs',
                'parent_menu_id' => $parent_menu_id,
                'person_code_required' => 0,
                'criteria_role_title' => 'Ir pasniedzēja loma',
                'criteria_role_field' => 'is_role_teacher'
            ]);
            
            $this->createUserList([
                'list_title' => 'Pakalpojumu sniedzēji',
                'item_title' => 'Pakalpojuma sniedzējs',
                'parent_menu_id' => $parent_menu_id,
                'person_code_required' => 0,
                'criteria_role_title' => 'Ir pakalpojuma sniedzēja loma',
                'criteria_role_field' => 'is_role_supply'
            ]);
            
            $list_id = $this->createUserList([
                'list_title' => 'Mācību dalībnieki',
                'item_title' => 'Mācību dalībnieks',
                'parent_menu_id' => $parent_menu_id,
                'person_code_required' => 1,
                'criteria_role_title' => 'Ir mācību dalībnieka loma',
                'criteria_role_field' => 'is_role_student'
            ]);
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'is_anonim',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_YES_NO,
                'title_list' => 'Ir anonīms',
                'title_form' => 'Ir anonīms',
                'hint' => 'Anonīmiem lietotājiem jānorāda e-pasts, kas būs jāizmanto autorizējoties kā lietotāja vārds. Veidojot sertifikātus, būs manuāli jānorāda vārds un uzvārds.',              
            ]);                                   
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['order_index' => 5]);
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'login_name',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
                'title_list' => 'E-pasts',
                'title_form' => 'E-pasts',
                'max_lenght' => 200,
                'hint' => 'E-pasts tiks izmantots kā lietotājvārds. Sistēma pēc anonīmā lietotāja izveidošanas, uz norādīto e-pastu nosūtīs uzaicinājumu reģistrēties sistēmā.',              
            ]);                                   
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['order_index' => 8]);
            
            
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (!$this->isEdu()) {
            return;
        }
        
        DB::transaction(function () {
            $list_id = DB::table('dx_lists')->where('list_title', '=', 'VAS koordinatori')->first()->id;
            \App\Libraries\DBHelper::deleteRegister($list_id);
            
            $list_id = DB::table('dx_lists')->where('list_title', '=', 'Iestāžu koordinatori')->first()->id;
            \App\Libraries\DBHelper::deleteRegister($list_id);
            
            $list_id = DB::table('dx_lists')->where('list_title', '=', 'Pasniedzēji')->first()->id;
            \App\Libraries\DBHelper::deleteRegister($list_id);
            
            $list_id = DB::table('dx_lists')->where('list_title', '=', 'Pakalpojumu sniedzēji')->first()->id;
            \App\Libraries\DBHelper::deleteRegister($list_id);
            
            $list_id = DB::table('dx_lists')->where('list_title', '=', 'Mācību dalībnieki')->first()->id;
            \App\Libraries\DBHelper::deleteRegister($list_id);
            
            DB::table('dx_menu')
                    ->where('title', '=', 'Lietotāji')
                    ->whereNull('parent_id')
                    ->delete();
        });
    }
    
    private function isEdu() {
        return Config::get('dx.is_edu_modules', false);
    }
    
    private function createUserList($arr_vals) {
        $list_id = App\Libraries\DBHelper::createUI([
            'table_name' => 'dx_users',
            'list_title' => $arr_vals['list_title'],
            'item_title' => $arr_vals['item_title'],
            'parent_menu_id' => $arr_vals['parent_menu_id']
        ]);
        
        $view = App\Libraries\DBHelper::getDefaultView($list_id);

        $fld_id = DB::table('dx_lists_fields')->insertGetId([
            'list_id' => $list_id,
            'db_name' => 'first_name',
            'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
            'title_list' => 'Vārds',
            'title_form' => 'Vārds',
            'max_lenght' => 50,
            'is_required' => 1
        ]);                                   
        App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['row_type_id' => 3]);
        App\Libraries\DBHelper::addFieldToView($list_id, $view->id, $fld_id);
                
        $fld_id = DB::table('dx_lists_fields')->insertGetId([
            'list_id' => $list_id,
            'db_name' => 'last_name',
            'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
            'title_list' => 'Uzvārds',
            'title_form' => 'Uzvārds',
            'max_lenght' => 50,
            'is_required' => 1
        ]);                                   
        App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['row_type_id' => 3]); 
        App\Libraries\DBHelper::addFieldToView($list_id, $view->id, $fld_id);
        
        $fld_id = DB::table('dx_lists_fields')->insertGetId([
            'list_id' => $list_id,
            'db_name' => 'person_code',
            'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
            'title_list' => 'Personas kods',
            'title_form' => 'Personas kods',
            'max_lenght' => 12,
            'is_required' => $arr_vals['person_code_required']
        ]);                                   
        App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['row_type_id' => 3]);
        App\Libraries\DBHelper::addFieldToView($list_id, $view->id, $fld_id);
        
        $fld_id = DB::table('dx_lists_fields')->insertGetId([
            'list_id' => $list_id,
            'db_name' => 'reg_addr_street',
            'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
            'title_list' => 'Adrese',
            'title_form' => 'Adrese',
            'max_lenght' => 200,
            'is_required' => 0
        ]);                                   
        App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['group_label' => 'Deklarētā dzīves vieta']);

        $fld_id = DB::table('dx_lists_fields')->insertGetId([
            'list_id' => $list_id,
            'db_name' => 'reg_addr_city',
            'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
            'title_list' => 'Pilsēta',
            'title_form' => 'Pilsēta',
            'max_lenght' => 100,
            'is_required' => 0
        ]);                                   
        App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['row_type_id' => 2]); 

        $fld_id = DB::table('dx_lists_fields')->insertGetId([
            'list_id' => $list_id,
            'db_name' => 'reg_addr_zip',
            'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
            'title_list' => 'Pasta indekss',
            'title_form' => 'Pasta indekss',
            'max_lenght' => 20,
            'is_required' => 0
        ]);                                   
        App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['row_type_id' => 2]); 

        $fld_id = DB::table('dx_lists_fields')->insertGetId([
            'list_id' => $list_id,
            'db_name' => $arr_vals['criteria_role_field'],
            'type_id' => App\Libraries\DBHelper::FIELD_TYPE_YES_NO,
            'title_list' => $arr_vals['criteria_role_title'],
            'title_form' => $arr_vals['criteria_role_title'],
            'default_value' => 1,
            'criteria' => 1,
            'operation_id' => 1
        ]);  
        App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id, ['is_hidden' => 1]);
                
        return $list_id;
    }
}
