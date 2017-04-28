<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUsersGendersAddPronounsUi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $list = App\Libraries\DBHelper::getListByTable('dx_users_genders');
        
        if (!$list) {
            return;
        }
        
        $list_id = $list->id;
        
        DB::transaction(function () use ($list_id){
                        
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'pronoun_start',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
                'title_list' => 'Pronoun start',
                'title_form' => 'Pronoun start',
                'max_lenght' => 10,
                'hint' => 'This pronoun can be used for Word documents generation - at the begining of a sentence'
            ]);
                                   
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id);
            
            $fld_id = DB::table('dx_lists_fields')->insertGetId([
                'list_id' => $list_id,
                'db_name' => 'pronoun_middle',
                'type_id' => App\Libraries\DBHelper::FIELD_TYPE_TEXT,
                'title_list' => 'Pronoun middle',
                'title_form' => 'Pronoun middle',
                'max_lenght' => 10,
                'hint' => 'This pronoun can be used for Word documents generation - in the middle of a sentence'
            ]);
                                   
            App\Libraries\DBHelper::addFieldToForm($list_id, $fld_id);
            
            DB::table('dx_users_genders')->where('id', '=', 4)->update(['pronoun_start' => 'She', 'pronoun_middle' => 'she']);
            DB::table('dx_users_genders')->where('id', '=', 5)->update(['pronoun_start' => 'He', 'pronoun_middle' => 'he']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $list = App\Libraries\DBHelper::getListByTable('dx_users_genders');
        
        if (!$list) {
            return;
        }
        
        $list_id = $list->id;
        
        DB::transaction(function () use ($list_id){            
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'pronoun_middle');
            App\Libraries\DBHelper::removeFieldCMS($list_id, 'pronoun_start');
        });
    }
}
