<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure\EduMigration;

class EduSubjectsAddPriceDuration extends EduMigration
{
    private $table_name = "edu_subjects";

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function edu_up()
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->text('price')->nullable()->comment = trans('db_' . $this->table_name.'.price');
            $table->text('duration')->nullable()->comment = trans('db_' . $this->table_name.'.duration');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function edu_down()
    {
        Schema::table($this->table_name, function (Blueprint $table) {
            $table->dropColumn(['price', 'duration']);
        });
    }
}
