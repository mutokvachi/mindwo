<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxUiThemesFillCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            DB::table('dx_ui_themes')
                    ->where('title', '=', 'Blue')
                    ->update([
                        'code' => 'mindwo-blue'
                    ]);
            
            DB::table('dx_ui_themes')
                    ->where('title', '=', 'Green')
                    ->update([
                        'code' => 'mindwo-green'
                    ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
