<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIconFieldToInSources extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_sources', function (Blueprint $table) {
            $table->string('icon_class', 200)->nullable();
        });
        
        DB::table('in_sources')->where('id', '=', 1)->update(['icon_class' => 'iconle-le_logo']);
        DB::table('in_sources')->where('id', '=', 2)->update(['icon_class' => 'fa fa-bolt']);
        DB::table('in_sources')->where('id', '=', 3)->update(['icon_class' => 'fa fa-plug']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_sources', function (Blueprint $table) {
            $table->dropColumn('icon_class');
        });
    }
}
