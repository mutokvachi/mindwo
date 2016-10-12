<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateObjectActionsJs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $file_js = storage_path() . "\\app\\updates\\2016_04_07_objec_actions.js";
        
        $content = File::get($file_js);
        DB::table('dx_forms_js')->where('id', '=', 9)->update(['js_code' => $content]);  
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
