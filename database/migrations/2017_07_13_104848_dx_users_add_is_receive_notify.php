<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUsersAddIsReceiveNotify extends Migration
{ 
    private $table_name = "dx_users";
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->boolean('is_receive_notify')->nullable()->default(false)->comment = trans('db_' . $this->table_name.'.is_receive_notify');
            $table->string('iban_nr', 50)->nullable()->comment = trans('db_' . $this->table_name.'.iban_nr');
            $table->index('is_receive_notify');
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
            $table->dropColumn(['is_receive_notify']);
            $table->dropColumn(['iban_nr']);
        });
    }
}
