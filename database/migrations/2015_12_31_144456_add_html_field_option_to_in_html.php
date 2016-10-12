<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddHtmlFieldOptionToInHtml extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_html', function (Blueprint $table) {
            $table->boolean('is_redactor')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_html', function (Blueprint $table) {
            $table->dropColumn('is_redactor');
        });
    }
}
