<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToDxUsersTeams extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        Schema::create('dx_users_teams', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title', 500)->nullable()->comment = "Title";
                        
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
        Schema::create('dx_users_jobtypes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title', 100)->nullable()->comment = "Title";
                        
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
        Schema::create('dx_users_term_reasons', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title', 100)->nullable()->comment = "Title";
                        
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
        Schema::create('dx_users_term_types', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title', 100)->nullable()->comment = "Title";
                        
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
        Schema::create('dx_location_types', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title', 500)->nullable()->comment = "Title";
                        
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
        Schema::create('dx_timezones', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title', 500)->nullable()->comment = "Title";
            $table->string('code', 5)->nullable()->comment = "Code";
            $table->integer('utc')->nullable()->comment = "UTC";
                        
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
        
        Schema::table('dx_users', function (Blueprint $table) {
            $table->integer('team_id')->nullable()->unsigned()->comment = "Team";            
            $table->index('team_id');
            $table->foreign('team_id')->references('id')->on('dx_users_teams');
            
            $table->integer('job_type_id')->nullable()->unsigned()->comment = "Job type";            
            $table->index('job_type_id');
            $table->foreign('job_type_id')->references('id')->on('dx_users_jobtypes');
            
            $table->integer('term_reason_id')->nullable()->unsigned()->comment = "Termination reason";            
            $table->index('term_reason_id');
            $table->foreign('term_reason_id')->references('id')->on('dx_users_term_reasons');
            
            $table->integer('reporting_manager_id')->nullable()->comment = "Reporting manager";            
            $table->index('reporting_manager_id');
            $table->foreign('reporting_manager_id')->references('id')->on('dx_users');
            
            $table->integer('term_type_id')->nullable()->unsigned()->comment="Termination type";
            $table->index('term_type_id');
            $table->foreign('term_type_id')->references('id')->on('dx_users_term_types');
            
            $table->integer('location_type_id')->nullable()->unsigned()->comment="Location type";
            $table->index('location_type_id');
            $table->foreign('location_type_id')->references('id')->on('dx_location_types');
            
            $table->integer('location_country_id')->nullable()->unsigned()->comment="Location country";
            $table->index('location_country_id');
            $table->foreign('location_country_id')->references('id')->on('dx_countries');
            
            $table->integer('timezone_id')->nullable()->unsigned()->comment="Location timezone";
            $table->index('timezone_id');
            $table->foreign('timezone_id')->references('id')->on('dx_timezones');
            
            $table->date('join_date')->nullable()->comment="Date of Joining";
            $table->date('prob_term_date')->nullable()->comment="Termination date of probation period";
            $table->date('termination_date')->nullable()->comment="Termination date";
            $table->string('location_city', 100)->nullable()->comment="Location city";
            
            $table->string('contract_file_name', 500)->nullable()->comment="Contract file name";
            $table->string('contract_file_guid', 50)->nullable()->comment="Contract file guid";
            
        });
        
        DB::table('dx_users_teams')->insert(['id' => 1, 'title' => 'Demo team 1']);
        DB::table('dx_users_teams')->insert(['id' => 2, 'title' => 'Demo team 2']);
                
        DB::table('dx_users_jobtypes')->insert(['id' => 1, 'title' => 'Full time']);
        DB::table('dx_users_jobtypes')->insert(['id' => 2, 'title' => 'Part time']);
        
        DB::table('dx_users_term_reasons')->insert(['id' => 1, 'title' => 'Bad attitude']);
        DB::table('dx_users_term_reasons')->insert(['id' => 2, 'title' => 'Not needed anymore']);
        DB::table('dx_users_term_reasons')->insert(['id' => 3, 'title' => 'Other']);
        
        DB::table('dx_users_term_types')->insert(['id' => 1, 'title' => 'Forced']);
        DB::table('dx_users_term_types')->insert(['id' => 2, 'title' => 'Temporary']);
        
        DB::table('dx_location_types')->insert(['id' => 1, 'title' => 'Office']);
        DB::table('dx_location_types')->insert(['id' => 2, 'title' => 'Remote']);
        
        DB::table('dx_timezones')->insert(['id' => 1, 'title' => 'Central European Time', 'utc' => 1, 'code' => 'CET']);
        DB::table('dx_timezones')->insert(['id' => 2, 'title' => 'Eastern European Time', 'utc' => 2, 'code' => 'EET']);
        DB::table('dx_timezones')->insert(['id' => 3, 'title' => 'Eastern Standard Time', 'utc' => -5, 'code' => 'EST']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_users', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropColumn(['team_id']);
            
            $table->dropForeign(['job_type_id']);
            $table->dropColumn(['job_type_id']);
            
            $table->dropForeign(['term_reason_id']);
            $table->dropColumn(['term_reason_id']);
            
            $table->dropForeign(['reporting_manager_id']);
            $table->dropColumn(['reporting_manager_id']);
            
            $table->dropForeign(['term_type_id']);
            $table->dropColumn(['term_type_id']);
            
            $table->dropForeign(['location_type_id']);
            $table->dropColumn(['location_type_id']);
            
            $table->dropForeign(['location_country_id']);
            $table->dropColumn(['location_country_id']);
            
            $table->dropForeign(['timezone_id']);
            $table->dropColumn(['timezone_id']);
            
            $table->dropColumn(['join_date']);
            $table->dropColumn(['prob_term_date']);
            $table->dropColumn(['termination_date']);
            $table->dropColumn(['location_city']);
            $table->dropColumn(['contract_file_name']);
            $table->dropColumn(['contract_file_guid']);
        });
        
        Schema::dropIfExists('dx_users_teams');
        Schema::dropIfExists('dx_users_jobtypes');
        Schema::dropIfExists('dx_users_term_reasons');
        
        Schema::dropIfExists('dx_users_term_types');
        Schema::dropIfExists('dx_location_types');
        Schema::dropIfExists('dx_timezones');
    }
}
