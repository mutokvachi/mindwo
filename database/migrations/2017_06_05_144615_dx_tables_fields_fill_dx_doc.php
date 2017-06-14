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
        $tbl = 'dx_doc';
        $flds = DB::table('dx_lists_fields as lf')
                ->select('lf.db_name', 'lf.type_id', 'ro.db_name as rel_table')
                ->join('dx_lists as l', 'lf.list_id', '=', 'l.id')
                ->join('dx_objects as o', 'l.object_id', '=', 'o.id')
                ->leftJoin('dx_lists as rl', 'lf.rel_list_id', '=', 'rl.id')
                ->leftJoin('dx_objects as ro', 'rl.object_id', '=', 'ro.id')
                ->where('o.db_name', '=', $tbl)
                ->where('lf.db_name', 'NOT LIKE', 'agr%')
                ->distinct()
                ->orderBy('ro.db_name', 'DESC')
                ->get();
        
        DB::transaction(function () use ($flds, $tbl){
            $fld_list = [];
            foreach($flds as $fld) {
                
                $field_obj = DB::connection()->getDoctrineColumn($tbl, $fld->db_name);
                $type = $field_obj->getType()->getName();
                $length = 0;
                
                if ($type == "string") {
                    $length = $field_obj->getLength();
                    if ($fld->rel_table) {
                        $fld->rel_table = null;
                        $fld->type_id = 1;
                    }
                }
                
                if ($fld->rel_table && substr($fld->db_name, strlen($fld->db_name)-3) != "_id") {
                    continue;
                }
                
                if (array_search($fld->db_name, $fld_list) === false) {
                    DB::table('dx_tables_fields')->insert([
                       'table_name' => 'dx_doc',
                       'field_name' => $fld->db_name,
                       'field_type_id' => $fld->type_id,
                       'rel_table_name' => $fld->rel_table,
                       'max_length' => $length,
                    ]);
                    
                    $fld_list[count($fld_list)] = $fld->db_name;
                }
                else {
                    \Log::info('Duplicate field: ' . $fld->db_name . " type id " . $fld->type_id . " rel_table = " . $fld->rel_table);
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
