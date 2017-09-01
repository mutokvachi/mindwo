<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduSubjectsGroupsAddPublish extends Migration
{
    private $table_name = "edu_subjects_groups";
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->datetime('first_publish')->nullable()->comment = trans('db_' . $this->table_name.'.first_publish');
            $table->boolean('is_complecting')->default(false)->nullable()->comment = trans('db_' . $this->table_name.'.is_complecting');
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
            $table->dropColumn(['first_publish']);
            $table->dropColumn(['is_complecting']);
        });
    }
}
