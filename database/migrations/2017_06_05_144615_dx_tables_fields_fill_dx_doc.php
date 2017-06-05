<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DxTablesFieldsFillDxDoc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $flds = DB::table('dx_lists_fields as lf')
                ->select('lf.db_name', 'lf.type_id')
                ->join('dx_lists as l', 'lf.list_id', '=', 'l.id')
                ->join('dx_objects as o', 'l.object_id', '=', 'o.id')
                ->where('o.db_name', '=', 'dx_doc')
                ->distinct()
                ->get();
        
        DB::transaction(function () use ($flds){
            $fld_list = [];
            foreach($flds as $fld) {
                
                if (array_search($fld->db_name, $fld_list) === false) {
                    DB::table('dx_tables_fields')->insert([
                       'table_name' => 'dx_doc',
                       'field_name' => $fld->db_name,
                       'field_type_id' => $fld->type_id
                    ]);
                    
                    $fld_list[count($fld_list)] = $fld->db_name;
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::transaction(function () {
            
            DB::table('dx_tables_fields')->where('table_name', '=', 'dx_doc')->delete();
            
        });
    }
}
