<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxListsFieldsLookupJs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        DB::transaction(function () {
            DB::table('dx_lists_fields')->where('id', '=', 26)->update(['binded_field_id' => null, 'binded_rel_field_id' => null]);
            
            // add custom JavaScript to form
            \App\Libraries\DBHelper::addJavaScriptToForm(7, '2017_04_24_dx_lists_fields.js', trans('db_dx_lists_fields.js_lookup'));
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
