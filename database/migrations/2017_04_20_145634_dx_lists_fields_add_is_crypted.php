<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxListsFieldsAddIsCrypted extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_lists_fields', function (Blueprint $table) {
            $table->boolean('is_crypted')->default(false)->comment = trans('db_dx_lists_fields.is_crypted');
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
            $table->dropColumn(['is_crypted']);
        });
    }
}
