<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use Illuminate\Support\Facades\Config;

class ChangeLeavesRequestRelation extends Migration
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
        
        if (!$this->is_hr_ui) {
            return;
        }
        
        $list_id = App\Libraries\DBHelper::getListByTable('dx_users_left')->id;    
        
        $rel_list_id = App\Libraries\DBHelper::getListByTable('dx_timeoff_types')->id;
        
        
        Schema::table('dx_users_left', function (Blueprint $table) {
            $table->dropForeign(['left_reason_id']);
            $table->dropIndex(['left_reason_id']);
        });
        
        Schema::disableForeignKeyConstraints();
        DB::table('dx_users_left')->update(['left_reason_id' => 1]);
        Schema::enableForeignKeyConstraints();
        
        Schema::table('dx_users_left', function (Blueprint $table) {
            $table->index('left_reason_id');
            $table->foreign('left_reason_id')->references('id')->on('dx_timeoff_types');
        });
        
        //fix relation
        DB::table('dx_lists_fields')
                ->where('list_id', '=', $list_id)
                ->where('db_name', '=', 'left_reason_id')
                ->update([
                    'rel_list_id'=>$rel_list_id,
                    'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $rel_list_id)->where('db_name', '=', 'title')->first()->id
                ]);
        
        //dx_users
        
        $list_id = App\Libraries\DBHelper::getListByTable('dx_users')->id;    
        
        $rel_list_id = App\Libraries\DBHelper::getListByTable('dx_timeoff_types')->id;
        
        Schema::table('dx_users', function (Blueprint $table) {
            $table->dropForeign(['left_reason_id']);
            $table->dropIndex(['left_reason_id']);
        });
        
        Schema::disableForeignKeyConstraints();
        DB::table('dx_users')->whereNotNull('left_reason_id')->update(['left_reason_id' => 1]);
        Schema::enableForeignKeyConstraints();
        
        Schema::table('dx_users', function (Blueprint $table) {
            $table->index('left_reason_id');
            $table->foreign('left_reason_id')->references('id')->on('dx_timeoff_types');
        });
        
        //fix relation
        DB::table('dx_lists_fields')
                ->where('list_id', '=', $list_id)
                ->where('db_name', '=', 'left_reason_id')
                ->update([
                    'rel_list_id'=>$rel_list_id,
                    'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $rel_list_id)->where('db_name', '=', 'title')->first()->id
                ]);
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->checkUI_Role();
        
        if (!$this->is_hr_ui) {
            return;
        }
        
        $list_id = App\Libraries\DBHelper::getListByTable('dx_users_left')->id;    
        
        $rel_list_id = App\Libraries\DBHelper::getListByTable('in_left_reasons')->id;
        
        Schema::table('dx_users_left', function (Blueprint $table) {
            $table->dropForeign(['left_reason_id']);
            $table->dropIndex(['left_reason_id']);
        });
        
        Schema::disableForeignKeyConstraints();
        DB::table('dx_users_left')->update(['left_reason_id' => 4]);
        Schema::enableForeignKeyConstraints();
        
        Schema::table('dx_users_left', function (Blueprint $table) {
            $table->index('left_reason_id');
            $table->foreign('left_reason_id')->references('id')->on('in_left_reasons');
        });
        
        //fix relation
        DB::table('dx_lists_fields')
                ->where('list_id', '=', $list_id)
                ->where('db_name', '=', 'left_reason_id')
                ->update([
                    'rel_list_id'=>$rel_list_id,
                    'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $rel_list_id)->where('db_name', '=', 'title')->first()->id
                ]);
        
        // dx_users
        $list_id = App\Libraries\DBHelper::getListByTable('dx_users')->id;    
        
        $rel_list_id = App\Libraries\DBHelper::getListByTable('in_left_reasons')->id;
        
        Schema::table('dx_users', function (Blueprint $table) {
            $table->dropForeign(['left_reason_id']);
            $table->dropIndex(['left_reason_id']);
        });
        
        Schema::disableForeignKeyConstraints();
        DB::table('dx_users')->whereNotNull('left_reason_id')->update(['left_reason_id' => 4]);
        Schema::enableForeignKeyConstraints();
        
        Schema::table('dx_users', function (Blueprint $table) {
            $table->index('left_reason_id');
            $table->foreign('left_reason_id')->references('id')->on('in_left_reasons');
        });
        
        //fix relation
        DB::table('dx_lists_fields')
                ->where('list_id', '=', $list_id)
                ->where('db_name', '=', 'left_reason_id')
                ->update([
                    'rel_list_id'=>$rel_list_id,
                    'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $rel_list_id)->where('db_name', '=', 'title')->first()->id
                ]);
    }
}
