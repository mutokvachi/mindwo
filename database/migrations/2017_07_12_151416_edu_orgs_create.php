<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduOrgsCreate extends Migration
{
    private $table_name = "edu_orgs";
    
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
            
            $table->string('title', 400)->comment = trans('db_' . $this->table_name.'.title');
            $table->integer('org_type_id')->unsigned()->comment = trans('db_' . $this->table_name.'.org_type_id');
            $table->string('reg_nr', 20)->comment = trans('db_' . $this->table_name.'.reg_nr');
            $table->string('address', 300)->comment = trans('db_' . $this->table_name.'.address');
            
            $table->index('org_type_id');            
            $table->foreign('org_type_id')->references('id')->on('edu_orgs_types');
            
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
