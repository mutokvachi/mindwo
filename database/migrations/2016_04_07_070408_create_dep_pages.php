<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepPages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_departments_pages', function (Blueprint $table) {
            $table->increments('id');            
            $table->string('title', 500)->nullable()->comment = "Nosaukums";
            $table->string('url', 1000)->nullable()->comment = "Vietnes URL";
            $table->integer('employee_id')->nullable()->unsigned()->comment = "Atbildīgais darbinieks";
            $table->integer('source_id')->nullable()->comment = "Datu avots";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('employee_id');
            $table->foreign('employee_id')->references('id')->on('in_employees');
            
            $table->index('source_id');
            $table->foreign('source_id')->references('id')->on('in_sources');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('in_departments_pages');
    }
}
