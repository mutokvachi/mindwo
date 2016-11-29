<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeLeavesRequestRelation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
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
