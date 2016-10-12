<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSysColorInDocumentsLotus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_documents_lotus', function (Blueprint $table) {
            $table->string('sys_color_class', 20)->nullable();
        });
        
        DB::table('in_documents_lotus')->where('id', '=', 1)->update(['sys_color_class' => 'green-jungle']);
        DB::table('in_documents_lotus')->where('id', '=', 2)->update(['sys_color_class' => 'blue-steel']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_documents_lotus', function (Blueprint $table) {
            $table->dropColumn('sys_color_class');
        });
    }
}
