<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxWorkflowsUpdateJsDirManager extends Migration
{
    private $js_title = 'Parāda vai paslēpj dokumenta lauka izvēlni atkarībā vai izpildītāja veids ir vai nav: Darbinieks no dokumenta, departamenta vadītājs vai struktūrvienības vadītājs';
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
            
            \App\Libraries\DBHelper::addJavaScriptToForm($this->table_name, '2017_01_15_workflows_dir_manager.js', $this->js_title);
            
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
