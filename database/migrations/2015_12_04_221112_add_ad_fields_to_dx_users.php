<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdFieldsToDxUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_users', function (Blueprint $table) {
            $table->boolean('is_blocked');
            $table->integer('auth_attempts')->default(0);
            $table->dateTime('last_attempt')->nullable();
            $table->string('ad_login', 200)->nullable();
            $table->index('ad_login');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_users', function (Blueprint $table) {
            $table->dropColumn('is_blocked');
            $table->dropColumn('ad_login');
            $table->dropColumn('auth_attempts');
            $table->dropColumn('last_attempt');
        });
    }
}
