<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxViewsUpdateJsListSet extends Migration
{
    private $js_title = 'Uzstāda reģistru pēc noklusēšanas tādu pašu kā skata reģistrs (no iepriekšējās formas)';
    private $table_name = 'dx_views_fields';
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            
            \App\Libraries\DBHelper::removeJavaScriptFromForm($this->table_name, $this->js_title);
            
            \App\Libraries\DBHelper::addJavaScriptToForm($this->table_name, '2017_01_11_views_list_set.js', $this->js_title);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
