<?php
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInDepartmentExternalId extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('in_departments', function (Blueprint $table) {
            $table->integer('external_id')
                            ->nullable()
                            ->unsigned()
                            ->index()
                    ->comment = "Ārējās sistēmas identifikators";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('in_departments', function (Blueprint $table) {
            $table->dropColumn(['external_id']);
        });
    }
}