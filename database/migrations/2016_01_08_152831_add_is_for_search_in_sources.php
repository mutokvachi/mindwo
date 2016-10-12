<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsForSearchInSources extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_sources', function (Blueprint $table) {
            $table->boolean('is_for_search')->default(0)->nullable();
            
            $table->index('is_for_search');
        });
        
        DB::table('in_sources')->where('id', "!=", 4)->update(['is_for_search' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_sources', function (Blueprint $table) {
            $table->dropColumn('is_for_search');
        });
    }
}
