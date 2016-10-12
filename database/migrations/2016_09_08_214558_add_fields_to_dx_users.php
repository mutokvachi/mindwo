<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToDxUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dx_users_genders', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title', 100)->nullable()->comment = "Title";
                        
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
        Schema::create('dx_users_positions', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title', 100)->nullable()->comment = "Title";
                        
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
        Schema::create('dx_users_employments', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title', 100)->nullable()->comment = "Title";
                        
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
        Schema::create('dx_countries', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title', 100)->nullable()->comment = "Title";
            $table->string('code', 3)->nullable()->comment = "Code";
                        
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
        Schema::table('dx_users', function (Blueprint $table) {
            $table->integer('gender_id')->nullable()->unsigned()->comment = "Gender";
            
            $table->index('gender_id');
            $table->foreign('gender_id')->references('id')->on('dx_users_genders');
            
            $table->integer('country_id')->nullable()->unsigned()->comment = "Nationality";
            
            $table->index('country_id');
            $table->foreign('country_id')->references('id')->on('dx_countries');
            
            $table->integer('employment_status_id')->nullable()->unsigned()->comment = "Employment status";
            
            $table->index('employment_status_id');
            $table->foreign('employment_status_id')->references('id')->on('dx_users_employments');
            
            $table->integer('position_id')->nullable()->unsigned()->comment = "Position";
            
            $table->index('position_id');
            $table->foreign('position_id')->references('id')->on('dx_users_positions');
        });
        
        DB::table('dx_users_genders')->insert(['id' => 1, 'title' => 'Male']);
        DB::table('dx_users_genders')->insert(['id' => 2, 'title' => 'Female']);
        
        DB::table('dx_countries')->insert(['id' => 1, 'title' => 'USA', 'code' => 'US']);
        DB::table('dx_countries')->insert(['id' => 2, 'title' => 'Russia', 'code' => 'RU']);
        DB::table('dx_countries')->insert(['id' => 3, 'title' => 'Latvia', 'code' => 'LV']);
        
        DB::table('dx_users_employments')->insert(['id' => 1, 'title' => 'Employee']);
        DB::table('dx_users_employments')->insert(['id' => 2, 'title' => 'Consultant']);
        DB::table('dx_users_employments')->insert(['id' => 3, 'title' => 'Other']);
        
        // ii) manager/employee/executive/c-level/director
        DB::table('dx_users_positions')->insert(['id' => 1, 'title' => 'Employee']);
        DB::table('dx_users_positions')->insert(['id' => 2, 'title' => 'Manager']);
        DB::table('dx_users_positions')->insert(['id' => 3, 'title' => 'Executive']);
        DB::table('dx_users_positions')->insert(['id' => 4, 'title' => 'C-level']);
        DB::table('dx_users_positions')->insert(['id' => 5, 'title' => 'Director']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_users', function (Blueprint $table) {
            $table->dropForeign(['gender_id']);
            $table->dropColumn(['gender_id']);
            
            $table->dropForeign(['country_id']);
            $table->dropColumn(['country_id']);
            
            $table->dropForeign(['employment_status_id']);
            $table->dropColumn(['employment_status_id']);
            
            $table->dropForeign(['position_id']);
            $table->dropColumn(['position_id']);
        });
        
        Schema::dropIfExists('dx_users_genders');
        Schema::dropIfExists('dx_countries');
        Schema::dropIfExists('dx_users_employments');
        Schema::dropIfExists('dx_users_positions');
    }
}
