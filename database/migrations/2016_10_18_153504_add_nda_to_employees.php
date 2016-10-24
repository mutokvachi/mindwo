<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Config;

class AddNdaToEmployees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // create new fields for nda & other files
        Schema::table('dx_users', function (Blueprint $table) {
            $table->string('nda_file_name', 500)->nullable()->comment = "NDA file name";
            $table->string('nda_file_guid', 20)->nullable()->comment = "NDA file GUID";
            
            $table->string('other_file_name', 500)->nullable()->comment = "Other file name";
            $table->string('other_file_guid', 20)->nullable()->comment = "Other file GUID";
        });
        
        $list_id = Config::get('dx.employee_list_id', 0);
        
        if ($list_id == 0) {
            return;
        }        
                
        $fld_id = DB::table('dx_lists_fields')->insertGetId([
            'list_id' => $list_id,
            'db_name' => 'nda_file_name',
            'type_id' => App\Libraries\DBHelper::FIELD_TYPE_FILE,
            'title_list' => 'NDA file',
            'title_form' => 'NDA file',
        ]);
        
        App\Libraries\DBHelper::addFieldToFormTab($list_id, $fld_id, 'Work details', 372);
        
        $fld_id = DB::table('dx_lists_fields')->insertGetId([
            'list_id' => $list_id,
            'db_name' => 'other_file_name',
            'type_id' => App\Libraries\DBHelper::FIELD_TYPE_FILE,
            'title_list' => 'Other file',
            'title_form' => 'Other file',
        ]);
        
        App\Libraries\DBHelper::addFieldToFormTab($list_id, $fld_id, 'Work details', 373);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    { 
        $list_id = Config::get('dx.employee_list_id', 0);
        
        if ($list_id > 0) {
            App\Libraries\DBHelper::dropField(Config::get('dx.employee_list_id'), 'nda_file_name');
            App\Libraries\DBHelper::dropField(Config::get('dx.employee_list_id'), 'other_file_name');
        }
        else {
            Schema::table('dx_users', function (Blueprint $table) {
                $table->dropColumn(['nda_file_name']);
                $table->dropColumn(['other_file_name']);            
            });
        }
        
        Schema::table('dx_users', function (Blueprint $table) {
            $table->dropColumn(['nda_file_guid']);
            $table->dropColumn(['other_file_guid']);            
        });                 
    }
}
