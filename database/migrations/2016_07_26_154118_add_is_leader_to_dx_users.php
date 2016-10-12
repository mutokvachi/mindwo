<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsLeaderToDxUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_users', function (Blueprint $table) {
            $table->boolean('is_leader')->nullable()->comment = "Ir struktūrvienības vadītājs";

            $table->unique(['is_leader', 'department_id']);
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
            $table->dropUnique(['is_leader', 'department_id']);
            $table->dropColumn(['is_leader']);
        });
    }
}
