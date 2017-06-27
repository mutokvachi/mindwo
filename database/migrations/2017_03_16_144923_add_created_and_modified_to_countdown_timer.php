<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCreatedAndModifiedToCountdownTimer extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_timer', function (Blueprint $table) {
            $table->integer('created_user_id')->nullable();
            $table->datetime('created_time')->nullable();
            $table->integer('modified_user_id')->nullable();
            $table->datetime('modified_time')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_timer', function (Blueprint $table) {
            $table->dropColumn('created_user_id');
            $table->dropColumn('created_time');
            $table->dropColumn('modified_user_id');
            $table->dropColumn('modified_time');
        });
    }
}
