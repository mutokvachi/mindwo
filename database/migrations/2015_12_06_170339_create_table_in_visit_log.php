<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableInVisitLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_visit_log', function (Blueprint $table) {
           $table->increments('id');
           $table->string('user_guid', 50)->nullable();
           $table->text('user_agent')->nullable();           
           $table->string('ip', 20)->nullable();
           $table->datetime('visit_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('in_visit_log');
    }
}
