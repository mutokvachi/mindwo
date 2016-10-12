<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProcessesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('in_processes', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name', 200)->comment = "Nosaukums";
            $table->string('code', 50)->comment = "Kods";
            $table->string('url', 250)->comment = "Piekļuves adrese";
            $table->string('user_name', 100)->comment = "Piekļuves lietotāja vārds";
            $table->string('password', 50)->comment = "Piekļuves parole";
            $table->tinyInteger('schedule_from')->comment = "Strādā no (stundas)";
            $table->tinyInteger('schedule_to')->comment = "Strādā līdz (stundas)";
            $table->integer('schedule_every_minutes')->comment = "Izpilda ik pēc noteiktajām minūtēm";
            $table->integer('employee_id')->unsigned()->comment = "Atbildīgais darbinieks";
            $table->dateTime('last_executed_time')->nullable()->comment = "Pēdējais procesa izpildes laiks";

            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();

            $table->index('employee_id');
            $table->foreign('employee_id')->references('id')->on('in_employees');
        });
        
        Schema::create('in_processes_log', function (Blueprint $table) {
            $table->increments('id');

            $table->boolean('is_success')->default(0)->comment = "Ir veiksmīga";
            $table->dateTime('register_time')->nullable()->comment = "Reģistrācijas laiks";
            $table->dateTime('start_time')->nullable()->comment = "Procesa sākums";
            $table->dateTime('end_time')->nullable()->comment = "Procesa beigas";
            $table->string('message', 500)->nullable()->comment = "Paziņojums";
            $table->integer('process_id')->unsigned()->comment = "Saistītais process";

            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();

            $table->index('process_id');
            $table->foreign('process_id')->references('id')->on('in_processes');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::dropIfExists('in_processes');
       Schema::dropIfExists('in_processes_log');
    }
}
