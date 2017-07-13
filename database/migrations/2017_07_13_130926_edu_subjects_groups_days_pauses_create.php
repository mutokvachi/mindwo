<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduSubjectsGroupsDaysPausesCreate extends Migration
{
    private $table_name = "edu_subjects_groups_days_pauses";
    
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists($this->table_name);
        
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');            
            
            $table->integer('group_day_id')->unsigned()->comment = trans($this->table_name.'.group_day_id');
            $table->time('time_from')->comment = trans($this->table_name.'.time_from');
            $table->time('time_to')->comment = trans($this->table_name.'.time_to');
            $table->integer('feed_org_id')->nullable()->unsigned()->comment = trans($this->table_name.'.feed_org_id');
            $table->text('notes')->nullable()->comment = trans($this->table_name.'.notes');
            
            $table->index('group_day_id');            
            $table->foreign('group_day_id')->references('id')->on('edu_subjects_groups_days');
            
            $table->index('feed_org_id');            
            $table->foreign('feed_org_id')->references('id')->on('edu_orgs');
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->table_name);
    }
}
