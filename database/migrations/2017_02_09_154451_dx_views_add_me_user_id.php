<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxViewsAddMeUserId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_views', function (Blueprint $table) {
            $table->integer('me_user_id')->nullable()->comment = 'Employees private view';
            
            $table->index('me_user_id');            
            $table->foreign('me_user_id')->references('id')->on('dx_users')->onDelete('cascade');
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
            $table->dropColumn(['me_user_id']);
        });
    }
}
