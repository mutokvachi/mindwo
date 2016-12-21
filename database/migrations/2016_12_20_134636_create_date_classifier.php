<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Inserts all years and month 70 years back from this day and 50 in future from this day.
 * This table is later used for example in report building when it is needed to get values for each month - then this table is joined with other table and data is grouped form input interval
 */
class CreateDateClassifier extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dx_date_classifiers', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->integer('year')->comment = "Year";
            $table->integer('month')->comment = "Month";

            $table->index('year');
            $table->index('month');
        });

        $start_time = new DateTime();
        $end_time = new DateTime();
        $start_year = $start_time->modify('-70 year')->format('Y');
        $end_year = $end_time->modify('+50 year')->format('Y');

        $sqlInsert = array();

        for ($y = $start_year; $y < $end_year; $y++) {
            for ($m = 1; $m <= 12; $m++) {
                array_push($sqlInsert, array('year' => $y, 'month' => $m));
            }
        }

        DB::table('dx_date_classifiers')->insert($sqlInsert);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dx_date_classifiers');
    }
}
