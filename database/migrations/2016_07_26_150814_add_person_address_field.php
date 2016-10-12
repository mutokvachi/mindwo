<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPersonAddressField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasColumn('dx_persons', 'address'))
        {
            Schema::table('dx_persons', function (Blueprint $table) {
                $table->string('address', 500)->nullable()->comment = "Adrese";

                $table->unique(['title', 'address']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_persons', function (Blueprint $table) {
            $table->dropUnique(['title', 'address']);
            $table->dropColumn(['address']);
        });
    }
}
