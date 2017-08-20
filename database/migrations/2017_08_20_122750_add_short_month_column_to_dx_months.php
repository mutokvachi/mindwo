<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShortMonthColumnToDxMonths extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dx_months', function (Blueprint $table) {
            $table->string('short_title', 50)->after('title')->nullable();
        });

        $months = \App\Models\Calendar\Month::all();
        foreach ($months as $m) {

            /*
             * 1) mēnešu nosaukumi īsināmi tradicionālajā veidā - janv., febr., apr., jūn., jūl., aug., sept., okt., nov., dec.;
             * 2) vienzilbīgos mēnešu nosaukumus marts un maijs neīsina.
             */
            switch ($m['nr']) {
                case 1:
                    $m->update(array("short_title" => 'Janv'));
                    break;
                case 2:
                    $m->update(array("short_title" => 'Febr'));
                    break;
                case 3:
                    $m->update(array("short_title" => 'Marts'));
                    break;
                case 4:
                    $m->update(array("short_title" => 'Apr'));
                    break;
                case 5:
                    $m->update(array("short_title" => 'Maijs'));
                    break;
                case 6:
                    $m->update(array("short_title" => 'Jūn'));
                    break;
                case 7:
                    $m->update(array("short_title" => 'Jūl'));
                    break;
                case 8:
                    $m->update(array("short_title" => 'Aug'));
                    break;
                case 9:
                    $m->update(array("short_title" => 'Sept'));
                    break;
                case 10:
                    $m->update(array("short_title" => 'Okt'));
                    break;
                case 11:
                    $m->update(array("short_title" => 'Nov'));
                    break;
                case 12:
                    $m->update(array("short_title" => 'Dec'));
                    break;
                default:
                    break;
            }

        }

        Schema::table('dx_months', function (Blueprint $table) {
            $table->string('short_title', 50)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dx_months', function (Blueprint $table) {
            $table->dropColumn('short_title');
        });
    }
}
