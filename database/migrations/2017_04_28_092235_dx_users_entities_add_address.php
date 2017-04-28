<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUsersEntitiesAddAddress extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_users_entities', function (Blueprint $table) {
            $table->string('address', 1000)->nullable()->comment = 'Address';
            $table->string('address_styled', 1000)->nullable()->comment = 'Styled address';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_users_entities', function (Blueprint $table) {
            $table->dropColumn(['address']);
            $table->dropColumn(['address_styled']);
        });
    }
}
