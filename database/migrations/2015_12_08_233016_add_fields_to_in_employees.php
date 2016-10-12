<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldsToInEmployees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_employees', function (Blueprint $table) {
            $table->string('phone', 50)->nullable();
            $table->string('mobile', 50)->nullable();
            $table->string('fax', 50)->nullable();
            $table->string('email', 250)->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->integer('source_id')->nullable();
            $table->string('department', 2000)->nullable();
            $table->string('position', 500)->nullable();
            $table->string('picture_name', 500)->nullable();
            $table->string('picture_guid', 50)->nullable();
            $table->index('source_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_employees', function (Blueprint $table) {
            $table->dropColumn('phone');
            $table->dropColumn('mobile');
            $table->dropColumn('fax');
            $table->dropColumn('email');
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
            $table->dropColumn('source_id');
            $table->dropColumn('department');
            $table->dropColumn('position');
            $table->dropColumn('picture_name');
            $table->dropColumn('picture_guid');
        });
    }
}
