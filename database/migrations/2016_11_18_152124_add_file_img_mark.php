<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFileImgMark extends Migration
{
    private $arrExt=['jpg', 'png', 'gif'];
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /*
        Schema::table('dx_files_headers', function (Blueprint $table) {
            $table->boolean('is_img')->nullable()->default(false)->comment = "Is image"; 
        });
        */
        DB::table('dx_files_headers')->whereIn('extention', $this->arrExt)->update(['is_img' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
         Schema::table('dx_files_headers', function (Blueprint $table) {
            $table->dropColumn(['is_img']);
         });
    }
}
