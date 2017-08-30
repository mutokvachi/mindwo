<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EduSubjectsAddDescription extends Migration
{
    private $table_name = "edu_subjects";

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->text('purpose')->nullable()->comment = trans('db_' . $this->table_name.'.purpose');
            $table->text('target_audience')->nullable()->comment = trans('db_' . $this->table_name.'.target_audience');
            $table->text('prerequisites')->nullable()->comment = trans('db_' . $this->table_name.'.prerequisites');
            $table->text('topics')->nullable()->comment = trans('db_' . $this->table_name.'.topics');
            $table->text('benefits')->nullable()->comment = trans('db_' . $this->table_name.'.benefits');

            $table->integer('coordinator_id')->nullable()->comment = trans('db_' . $this->table_name.'.coordinator_id');
            
            $table->index('coordinator_id');            
            $table->foreign('coordinator_id')->references('id')->on('dx_users');
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
            $table->dropForeign(['coordinator_id']);
            $table->dropIndex(['coordinator_id']);
            $table->dropColumn(['purpose', 'target_audience', 'prerequisites', 'topics', 'benefits', 'coordinator_id']);
        });
    }
}
