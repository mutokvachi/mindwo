<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDbHistoryTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dx_db_event_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 100)->nullable()->comment = "Nosaukums";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->unique('title');
        });
        
        DB::table('dx_db_event_types')->insert([
            ['title' => 'Jauns'],
            ['title' => 'Labošana'],
            ['title' => 'Dzēšana']
        ]); 
        
        Schema::create('dx_db_events', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type_id')->nullable()->unsigned()->comment = "Notikuma veids";
            $table->integer('user_id')->nullable()->comment = "Lietotājs";
            $table->datetime('event_time')->nullable()->comment = "Notikuma laiks";
            $table->integer('list_id')->nullable()->comment = "Reģistrs";
            $table->integer('item_id')->nullable()->unsigned()->comment = "Ieraksta ID";
            
            $table->index('type_id');
            $table->index('user_id');
            $table->index('list_id');
            $table->index('item_id');
        });
        
        Schema::table('dx_db_events', function (Blueprint $table) {
            $table->foreign('type_id')->references('id')->on('dx_db_event_types');
            $table->foreign('user_id')->references('id')->on('dx_users');
            $table->foreign('list_id')->references('id')->on('dx_lists');
        });
        
        Schema::create('dx_db_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('event_id')->nullable()->unsigned()->comment = "Notikums";
            $table->integer('field_id')->nullable()->comment = "Lauks";
            $table->text('old_val_txt')->nullable()->comment = "Vecā vērtība";
            $table->text('new_val_txt')->nullable()->comment = "Jaunā vērtība";
            $table->integer('old_val_rel_id')->nullable()->comment = "Vecais saistītā ieraksta ID";
            $table->integer('new_val_rel_id')->nullable()->comment = "Jaunais saistītā ieraksta ID";
            $table->string('old_val_file_name', 500)->nullable()->comment = "Vecais datnes nosaukums";
            $table->string('new_val_file_name', 500)->nullable()->comment = "Jaunais datnes nosaukums";
            $table->string('old_val_file_guid', 50)->nullable()->comment = "Vecais datnes GUID";
            $table->string('new_val_file_guid', 50)->nullable()->comment = "Jaunais datnes GUID";
            
            $table->index('event_id');
            $table->index('field_id');
        });
        
         Schema::table('dx_db_history', function (Blueprint $table) {
            $table->foreign('event_id')->references('id')->on('dx_db_events');
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
        Schema::dropIfExists('dx_db_history');
        Schema::dropIfExists('dx_db_events');
        Schema::dropIfExists('dx_db_event_types');
    }
}
