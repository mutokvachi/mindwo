<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxListsJsUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            DB::table('dx_forms_js')->where('form_id', '=', 4)->delete();
            \App\Libraries\DBHelper::addJavaScriptToForm('dx_lists', '2017_07_24_dx_lists.js', trans('db_dx_lists.js_add_menus'));
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
