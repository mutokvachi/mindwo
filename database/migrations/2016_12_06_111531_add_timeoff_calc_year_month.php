<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Adds two new columns with indexing which extract year and month from calc_date column
 */
class AddTimeoffCalcYearMonth extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {        
        Schema::table('dx_timeoff_calc', function (Blueprint $table) {
            $table->integer('calc_date_year')->nullable()->comment = "Year";
            $table->integer('calc_date_month')->nullable()->comment = "Month";

            $table->index(['calc_date_year', 'calc_date_month']);
        });

        DB::unprepared("CREATE TRIGGER tr_dx_timeoff_calc_insert BEFORE INSERT ON dx_timeoff_calc FOR EACH ROW 
            BEGIN
                SET new.calc_date_year = YEAR(new.calc_date);
                SET new.calc_date_month = MONTH(new.calc_date);
            END;");

        DB::unprepared("CREATE TRIGGER tr_dx_timeoff_calc_update BEFORE UPDATE ON dx_timeoff_calc FOR EACH ROW 
            BEGIN
                SET new.calc_date_year = YEAR(new.calc_date);
                SET new.calc_date_month = MONTH(new.calc_date);
            END;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER `tr_dx_timeoff_calc_insert`');
        DB::unprepared('DROP TRIGGER `tr_dx_timeoff_calc_update`');

        Schema::table('dx_timeoff_calc', function (Blueprint $table) {
            $table->dropIndex(['calc_date_year', 'calc_date_month']);
            $table->dropColumn(['calc_date_year', 'calc_date_month']);
        });
    }
}
