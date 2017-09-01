<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduOrgsUsersCreate extends Migration
{
    private $table_name = "edu_orgs_users";
    
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
            
            $table->integer('user_id')->comment = trans('db_' . $this->table_name.'.user_id');
            $table->integer('org_id')->unsigned()->comment = trans('db_' . $this->table_name.'.org_id');            
            $table->string('job_title', 250)->nullable()->comment = trans('db_' . $this->table_name.'.job_title');
            $table->string('email', 200)->nullable()->comment = trans('db_' . $this->table_name.'.email');
            $table->string('phone', 20)->nullable()->comment = trans('db_' . $this->table_name.'.phone');
            $table->string('mobile', 20)->nullable()->comment = trans('db_' . $this->table_name.'.mobile');
            $table->date('end_date')->nullable()->comment = trans('db_' . $this->table_name.'.terminated');
            
            $table->index('org_id');            
            $table->foreign('org_id')->references('id')->on('edu_orgs');
            
            $table->index('user_id');            
            $table->foreign('user_id')->references('id')->on('dx_users');
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable(); 
            
            $table->unique(['user_id', 'org_id']);
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
