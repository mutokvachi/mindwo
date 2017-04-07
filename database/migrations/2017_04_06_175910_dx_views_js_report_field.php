<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxViewsJsReportField extends Migration
{
    private $js_title = 'Ielādē atskaites sadaļas datuma laukus atbilstoši reģistram';
    private $table_name = 'dx_views';
    private $js_file_name = '2017_04_06_view_report_field.js';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            
            \App\Libraries\DBHelper::removeJavaScriptFromForm($this->table_name, $this->js_title);
            
            \App\Libraries\DBHelper::addJavaScriptToForm($this->table_name, $this->js_file_name, $this->js_title);
            
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
