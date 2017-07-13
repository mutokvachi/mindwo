<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUsersAddSignatureFile extends Migration
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
            $table->string('sign_file_name', 500)->nullable()->comment = trans($this->table_name.'.sign_file_name');
            $table->string('sign_file_guid', 50)->nullable()->comment = trans($this->table_name.'.sign_file_guid');
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
            $table->dropColumn(['sign_file_name']);
            $table->dropColumn(['sign_file_guid']);
        });
    }
}
