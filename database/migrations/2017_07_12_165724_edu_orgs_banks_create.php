<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduOrgsBanksCreate extends Migration
{ 
    private $table_name = "edu_orgs_banks";
    
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
            
            $table->integer('org_id')->unsigned()->comment = trans($this->table_name.'.org_id');
            $table->integer('bank_id')->unsigned()->comment = trans($this->table_name.'.bank_id');
            
            $table->index('org_id');            
            $table->foreign('org_id')->references('id')->on('edu_orgs')->onDelete('cascade');
            
            $table->index('bank_id');            
            $table->foreign('bank_id')->references('id')->on('edu_banks');
            
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
