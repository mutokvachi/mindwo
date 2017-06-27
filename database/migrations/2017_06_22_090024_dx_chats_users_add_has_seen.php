<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxChatsUsersAddHasSeen extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_chats_users', function (Blueprint $table) {
            $table->boolean('has_seen')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_chats_users', function (Blueprint $table) {
            $table->dropColumn(['has_seen']);            
        });
    }
}
