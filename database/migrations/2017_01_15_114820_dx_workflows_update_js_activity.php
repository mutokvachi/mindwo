<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxWorkflowsUpdateJsActivity extends Migration
{
    private $js_title = 'Parāda vai paslēpj darbinieka uzdevuma laukus, ja izvēlēts sistēmisks uzdevums';
    private $table_name = 'dx_workflows';
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::transaction(function () {
            
            \App\Libraries\DBHelper::removeJavaScriptFromForm($this->table_name, $this->js_title);
            
            \App\Libraries\DBHelper::addJavaScriptToForm($this->table_name, '2017_01_15_workflows_activity.js', $this->js_title);
            
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
