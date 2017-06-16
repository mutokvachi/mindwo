<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Libraries\Structure;

class HdOfficeUi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $klasif_menu = DB::table('dx_menu')->where('title', '=', 'Klasifikatori')->first();
            
        if (!$klasif_menu) {
            return;
        }
            
        DB::transaction(function () use ($klasif_menu) {
            DB::table('dx_menu')->where('title', '=', 'Atbalsts')->update(['title' => 'IT atbalsts']);
                        
            $parent_id = DB::table('dx_menu')->insertGetId(['parent_id' => $klasif_menu->id, 'title'=>'Biroja atbalsts', 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', $klasif_menu->id)->max('order_index') + 10)]);
            
            $arr = $this->getTables();
            foreach($arr as $tbl) {
                
                $obj = DB::table('dx_objects')->where('db_name', '=', $tbl)->first();

                if ($obj) {
                    
                                       
                    $list = \App\Libraries\DBHelper::getListByTable($tbl);

                    if ($list && $parent_id) {
                        
                        $dub_list = DB::table('dx_lists')->where('object_id', '=', $obj->id)->where('list_title', '=', $list->list_title . ' - biroja atbalsts')->first();
                    
                        if ($dub_list) {
                            \App\Libraries\DBHelper::deleteRegister($dub_list->id);
                        }
                        
                        $list_copy = new Structure\StructMethod_register_copy();
                        $list_copy->obj_id = $obj->id;
                        $list_copy->list_id = $list->id;
                        $list_copy->register_title = $list->list_title . ' - biroja atbalsts';
                        $list_copy->doMethod();
                        
                        // user rights
                        DB::table('dx_roles_lists')->insert(['role_id' => 1, 'list_id' => $list_copy->new_list_id, 'is_edit_rights' => 1, 'is_delete_rights' => 1, 'is_new_rights' => 1]); // Sys admins

                        // menu
                        if ($parent_id) {
                            DB::table('dx_menu')->insert(['parent_id' => $parent_id, 'list_id' => $list_copy->new_list_id, 'title'=>$list_copy->register_title, 'order_index' => (DB::table('dx_menu')->where('parent_id', '=', $parent_id)->max('order_index') + 10)]);
                        }
                        
                        if ($tbl == 'dx_hd_request_types') {
                            
                            // update relation to parent item
                            DB::table('dx_lists_fields')
                                    ->where('list_id', '=', $list_copy->new_list_id)
                                    ->where('db_name', '=', 'parent_id')
                                    ->update([
                                        'rel_list_id' => $list_copy->new_list_id,
                                        'rel_display_field_id' => DB::table('dx_lists_fields')->where('list_id', '=', $list_copy->new_list_id)->where('db_name', '=', 'title')->first()->id
                                    ]);
                            
                        }
                    }
                    
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
        
        $menu = DB::table('dx_menu')->where('title', '=', 'Biroja atbalsts')->first();
        
        if (!$menu) {
            return;
        }
        
        DB::transaction(function () use ($menu) {
            
            $arr = $this->getTables();
            foreach($arr as $tbl) {
               $obj = DB::table('dx_objects')->where('db_name', '=', $tbl)->first();

                if ($obj) {
                    
                    if ($menu) {
                        $list = DB::table('dx_lists as l')
                                ->join('dx_menu as m', 'm.list_id', '=', 'l.id')
                                ->where('l.object_id', '=', $obj->id)
                                ->where('m.parent_id','=',$menu->id)
                                ->first();
                        
                        
                        \App\Libraries\DBHelper::deleteRegister($list->id);
                        
                    }
                } 
            }
            
            DB::table('dx_menu')->where('id', '=', $menu->id)->delete(); 
        });
    }
    
    private function getTables() {
        return ['dx_hd_inner_types', 'dx_hd_priorities', 'dx_hd_request_types', 'dx_hd_statuses'];
    }
}
