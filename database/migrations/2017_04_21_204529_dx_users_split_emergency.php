<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUsersSplitEmergency extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_users', function (Blueprint $table) {
            $table->integer('emergency1_type_id')->nullable()->unsigned()->comment = trans('db_dx_users.emergency1_type_id');
            $table->string('emergency1_name', 100)->nullable()->comment = trans('db_dx_users.emergency1_name');
            $table->string('emergency1_phone', 100)->nullable()->comment = trans('db_dx_users.emergency1_phone');
            
            $table->index('emergency1_type_id');            
            $table->foreign('emergency1_type_id')->references('id')->on('dx_users_emergency_types');
            
            $table->integer('emergency2_type_id')->nullable()->unsigned()->comment = trans('db_dx_users.emergency2_type_id');
            $table->string('emergency2_name', 100)->nullable()->comment = trans('db_dx_users.emergency2_name');
            $table->string('emergency2_phone', 100)->nullable()->comment = trans('db_dx_users.emergency2_phone');
            
            $table->index('emergency2_type_id');            
            $table->foreign('emergency2_type_id')->references('id')->on('dx_users_emergency_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_users', function (Blueprint $table) {            
            $table->dropForeign(['emergency1_type_id']);
            $table->dropColumn(['emergency1_type_id']);
            
            $table->dropForeign(['emergency2_type_id']);
            $table->dropColumn(['emergency2_type_id']);
            
            $table->dropColumn(['emergency1_name']);
            $table->dropColumn(['emergency1_phone']);
            $table->dropColumn(['emergency2_name']);
            $table->dropColumn(['emergency2_phone']);
            
        });
    }
}
