<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomPhpScriptToForm extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dx_custom_php', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 500)->nullable()->comment = "Nosaukums";
            $table->string('url', 100)->nullable()->comment = "URL";
            $table->text('php_code')->nullable()->comment = "PHP kods";
            $table->integer('role_id')->nullable()->index()->comment = "Loma";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->unique('url');
            $table->foreign('role_id')->references('id')->on('dx_roles');
        });   
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {  
        Schema::dropIfExists('dx_custom_php');
    }
}
