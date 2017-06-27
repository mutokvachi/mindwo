<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxViewsAddIsForLookup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_views', function (Blueprint $table) {
            $table->boolean('is_for_lookup')->nullable()->default(false)->comment = trans('db_dx_views.is_for_lookup');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_views', function (Blueprint $table) {
            $table->dropColumn(['is_for_lookup']);
        });
    }
}
