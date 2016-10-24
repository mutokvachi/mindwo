<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 *  Add column to specify which roles will be set as default for new users
 */
class AddDefaultPermissions extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_roles', function (Blueprint $table) {
            $table->boolean('is_default')->default(0)->comment = "Default role for new users";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_roles', function ($table) {
            $table->dropColumn('is_default');
        });
    }
}
