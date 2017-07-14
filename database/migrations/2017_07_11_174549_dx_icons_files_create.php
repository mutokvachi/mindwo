<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxIconsFilesCreate extends Migration
{
    private $table_name = "dx_icons_files";
    
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
            $table->string('file_name', 500)->nullable()->comment = trans('db_' . $this->table_name.'.file_name');
            $table->string('file_guid', 50)->nullable()->comment = trans('db_' . $this->table_name.'.file_guid');
            
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
