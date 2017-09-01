<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUsersAddEduDescription extends Migration
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
            $table->text('introduction')->nullable()->comment = trans('db_' . $this->table_name.'.introduction');
            $table->text('experience')->nullable()->comment = trans('db_' . $this->table_name.'.experience');
            $table->text('education')->nullable()->comment = trans('db_' . $this->table_name.'.education');
            $table->text('additional_info')->nullable()->comment = trans('db_' . $this->table_name.'.additional_info');
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
            $table->dropColumn(['introduction', 'experience', 'education', 'additional_info']);
        });
    }    
}
