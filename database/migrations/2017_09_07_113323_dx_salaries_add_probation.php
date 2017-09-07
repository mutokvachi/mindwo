<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxSalariesAddProbation extends Migration
{
    private $table_name = "dx_users_salaries";
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->string('probation_salary', 1000)->nullable()->comment = trans('db_' . $this->table_name.'.probation_salary');
            $table->integer('probation_months')->default(0)->nullable()->comment = trans('db_' . $this->table_name.'.probation_months');
            $table->string('probation_salary_annual', 1000)->nullable()->comment = trans('db_' . $this->table_name.'.probation_salary_annual');
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
            $table->dropColumn(['probation_salary']);
            $table->dropColumn(['probation_months']);
            $table->dropColumn(['probation_salary_annual']);
        });
    }
}
