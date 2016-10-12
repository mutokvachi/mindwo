<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFileFieldIsTextExtract extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_lists_fields', function (Blueprint $table) {
            $table->boolean('is_text_extract')
                    ->default(0)
                    ->nullable()
                    ->comment = "Ir jāizgūst teksts";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_lists_fields', function (Blueprint $table) {
            $table->dropColumn(['is_text_extract']);
        });
    }
}
