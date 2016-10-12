<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProcesses extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_processes', function (Blueprint $table) {
            $table->string('user_name', 100)->nullable()->change();
            $table->string('password', 50)->nullable()->change();
        });

        $lotus_process = new \App\Process();
        $lotus_process->name = 'Dokumentu sinhronizēšana';
        $lotus_process->code = 'LOTUSNOTES';
        $lotus_process->schedule_from = 1;
        $lotus_process->schedule_to = 2;
        $lotus_process->schedule_every_minutes = 65;
        $lotus_process->employee_id = 1;
        $lotus_process->save();

        $lotus_sys = \App\Models\DocumentsLotus::find(1);
        $lotus_sys->json_url = 'http://local.le/rest_test/#readviewentries#/#JSON#/#startCounter#/#download_count#';
        $lotus_sys->source_id = 1;
        $lotus_sys->save();

        $lotus_sys = \App\Models\DocumentsLotus::find(2);
        $lotus_sys->json_url = 'http://local.le/rest_test/#readviewentries#/#JSON#/#startCounter#/#download_count#';
        $lotus_sys->source_id = 3;
        $lotus_sys->save();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_processes', function (Blueprint $table) {
            $table->string('user_name', 100)->nullable(false)->change();
            $table->string('password', 50)->nullable(false)->change();
        });
    }
}