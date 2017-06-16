<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxHdRequestTypesAddOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_hd_request_types', function (Blueprint $table) {
            $table->boolean('is_work_place')->nullable()->default(false)->comment="Ir darba vietas lauks";
            $table->boolean('is_mobile')->nullable()->default(false)->comment="Ir mobilā tālruņa veida lauks";
            $table->boolean('is_mobilly')->nullable()->default(false)->comment="Ir mobilly veida lauks";
            $table->boolean('is_empl')->nullable()->default(false)->comment="Ir darbinieka lauks";
            $table->boolean('is_mobnr')->nullable()->default(false)->comment="Ir tālruņa numura lauks";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_hd_request_types', function (Blueprint $table) {
            $table->dropColumn(['is_work_place']);
            $table->dropColumn(['is_mobile']);
            $table->dropColumn(['is_mobilly']);
            $table->dropColumn(['is_empl']);
            $table->dropColumn(['is_mobnr']);
        });
    }
}
