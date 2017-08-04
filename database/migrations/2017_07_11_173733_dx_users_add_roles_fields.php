<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUsersAddRolesFields extends Migration
{
    private $table_name = 'dx_users';
    
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_users', function (Blueprint $table) {
            $table->boolean('is_role_coordin_main')->nullable()->default(false)->comment = trans('db_' . $this->table_name . '.criteria_role_title_edu');
            $table->boolean('is_role_coordin')->nullable()->default(false)->comment = trans('db_' . $this->table_name . '.criteria_role_title_org');
            $table->boolean('is_role_teacher')->nullable()->default(false)->comment = trans('db_' . $this->table_name . '.criteria_role_title_teacher');
            $table->boolean('is_role_student')->nullable()->default(false)->comment = trans('db_' . $this->table_name . '.criteria_role_title_student');
            $table->boolean('is_role_supply')->nullable()->default(false)->comment = trans('db_' . $this->table_name . '.criteria_role_title_serv');
            $table->boolean('is_anonim')->nullable()->default(false)->comment = trans('db_' . $this->table_name . '.is_anonim');
            
            $table->integer('region_id')->nullable()->unsigned()->comment = trans('db_' . $this->table_name.'.region_id');
            
            $table->index('region_id');            
            $table->foreign('region_id')->references('id')->on('dx_regions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->dropColumn(['is_role_coordin_main']);
            $table->dropColumn(['is_role_coordin']);
            $table->dropColumn(['is_role_teacher']);
            $table->dropColumn(['is_role_student']);
            $table->dropColumn(['is_role_supply']);
            $table->dropColumn(['is_anonim']);
            $table->dropForeign(['region_id']);
            $table->dropColumn(['region_id']);
        });
    }
}
