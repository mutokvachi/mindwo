<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUsersGendersAddPronouns extends Migration
{
     /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_users_genders', function (Blueprint $table) {
            $table->string('pronoun_start', 10)->nullable()->comment = 'Pronoun start';
            $table->string('pronoun_middle', 10)->nullable()->comment = 'Pronoun middle';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_users_genders', function (Blueprint $table) {
            $table->dropColumn(['pronoun_start']);
            $table->dropColumn(['pronoun_middle']);
        });
    }
}
