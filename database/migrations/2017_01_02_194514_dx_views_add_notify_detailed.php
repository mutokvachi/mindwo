<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxViewsAddNotifyDetailed extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_views', function (Blueprint $table) {
            $table->boolean('is_detailed_notify')->default(false)->nullable()->comment = trans('db_dx_views.is_detailed_notify_list');            
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
            $table->dropColumn(['is_detailed_notify']);
        });
    }
}
