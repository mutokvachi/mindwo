<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxListsFieldsCryptedJs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $list = App\Libraries\DBHelper::getListByTable('dx_lists_fields');
        
        if (!$list) {
            return;
        }
        
        $list_id = $list->id;
        
        DB::transaction(function () use ($list_id){
            
            \App\Libraries\DBHelper::removeJavaScriptFromForm('dx_lists_fields', 'Parāda vai paslēpj laukus atkarībā no izvēlētā tipa');
            
            \App\Libraries\DBHelper::addJavaScriptToForm('dx_lists_fields', '2017_04_26_dx_lists_fields_crypto.js', 'Parāda vai paslēpj laukus atkarībā no izvēlētā tipa');
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
