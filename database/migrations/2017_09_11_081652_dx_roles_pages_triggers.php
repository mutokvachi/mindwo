<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxRolesPagesTriggers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->makeTrigger('dx_roles_pages', 'insert');
        $this->makeTrigger('dx_roles_pages', 'update');
        $this->makeTrigger('dx_roles_pages', 'delete');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->deleteTrigger('dx_roles_pages', 'insert');
        $this->deleteTrigger('dx_roles_pages', 'update');
        $this->deleteTrigger('dx_roles_pages', 'delete');
    }

    private function makeTrigger($tbl_name, $operation) {
        $sql = 
        "CREATE TRIGGER `tr_" . $tbl_name . "_" . $operation . "` BEFORE " . $operation . " ON `" . $tbl_name . "`
            FOR EACH ROW
                UPDATE in_last_changes SET change_time=now() WHERE code='MENU';            
        ";
        
        DB::connection()->getPdo()->exec($sql);
    }
    
    private function deleteTrigger($tbl_name, $operation) {
        DB::connection()->getPdo()->exec('drop trigger if exists tr_' . $tbl_name . '_' . $operation);
    }
}
