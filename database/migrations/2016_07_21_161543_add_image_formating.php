<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImageFormating extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dx_files_paths', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('field_id')->nullable()->comment = "Lauks";
            $table->string('folder_path', 1000)->nullable()->comment = "Foldera ceļš";
            $table->integer('width')->nullable()->comment = "Platums, px";
            $table->integer('height')->nullable()->comment = "Augstums, px";
            $table->boolean('is_for_gallery')->nullable()->comment = "Ir galeriju attēls";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('field_id');
            $table->foreign('field_id')->references('id')->on('dx_lists_fields');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dx_files_paths');
    }
}
