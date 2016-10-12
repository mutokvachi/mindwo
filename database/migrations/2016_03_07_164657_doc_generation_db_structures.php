<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DocGenerationDbStructures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dx_lists_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 200)->nullable()->comment = "Nosaukums";
            $table->integer('order_index')->default(0)->comment = "Secība";
            
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
            
            $table->index('order_index');
        });
        
        Schema::table('dx_lists', function (Blueprint $table) {
            $table->integer('group_id')->unsigned()->nullable()->comment = "Grupa";
            $table->text('hint')->nullable()->comment = "Paskaidrojums";
            
            $table->index('group_id');
            $table->foreign('group_id')->references('id')->on('dx_lists_groups');
        });
        
        Schema::table('dx_lists_fields', function (Blueprint $table) {
            $table->text('hint')->nullable()->comment = "Paskaidrojums";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_lists', function (Blueprint $table) {
            $table->dropForeign(['group_id']);   
            $table->dropColumn(['group_id']);
            $table->dropColumn(['hint']);
        });
        
        Schema::dropIfExists('dx_lists_groups');
        
        Schema::table('dx_lists_fields', function (Blueprint $table) {
            $table->dropColumn(['hint']);
        });
    }
}
