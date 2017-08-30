<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduPublishValidatorsCreate extends Migration
{
    private $table_name = "edu_publish_validators";
    
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
            
            $table->string('title', 250)->comment = trans('db_' . $this->table_name.'.title');
            $table->string('code', 50)->comment = trans('db_' . $this->table_name.'.code');
            $table->boolean('is_for_publish')->default(true)->comment = trans('db_' . $this->table_name.'.is_for_publish');
            $table->boolean('is_for_complect')->default(true)->comment = trans('db_' . $this->table_name.'.is_for_complect');
                        
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable(); 
            
            $table->unique(['code']);
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
