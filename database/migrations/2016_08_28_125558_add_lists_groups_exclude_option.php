<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddListsGroupsExcludeOption extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_lists_groups', function (Blueprint $table) {
            $table->boolean('is_not_in_docs')->nullable()->default(0)->comment = "Nerādīt ģenerētajā dokumentācijā";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_lists_groups', function (Blueprint $table) {
            $table->dropColumn(['is_not_in_docs']);
        });
    }
}
