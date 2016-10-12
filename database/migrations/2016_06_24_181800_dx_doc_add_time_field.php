<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxDocAddTimeField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_doc', function (Blueprint $table) {
            $table->datetime('event_time')->nullable()->comment = "Datums un laiks";
            $table->text('notes1')->nullable()->comment = "Papildus piezīmes";
            $table->text('notes2')->nullable()->comment = "Papildus piezīmes";
            $table->integer('dx_item_status_id')->nullable()->comment = "Dokumenta statuss";
            
            $table->index('dx_item_status_id');
            $table->foreign('dx_item_status_id')->references('id')->on('dx_item_statuses');
            
            $table->decimal('ammount', 8, 2)->nullable()->default(0)->change();
            	
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_doc', function (Blueprint $table) {
            $table->dropColumn(['event_time']);
            $table->dropColumn(['notes1']);
            $table->dropColumn(['notes2']);
            $table->dropColumn(['ammount']);
            
            $table->dropForeign(['dx_item_status_id']);
            $table->dropColumn(['dx_item_status_id']);
        });
    }
}
