<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduRoomsAddAddress extends Migration
{
    private $table_name = "edu_rooms";
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->string('room_address', 250)->nullable()->comment = trans('db_' . $this->table_name.'.room_address');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->dropColumn(['room_address']);
        });
    }
}
