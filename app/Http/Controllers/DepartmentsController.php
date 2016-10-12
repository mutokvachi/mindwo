<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DB;
use PDO;
use Config;

class DepartmentsController extends Controller
{
    /**
      * Struktūrvienību attēlošanas kontrolieris      *
     */
    
    /**
     * Atgriež departamentu klasifikatoru koka veidā
     * 
     * @return Response JSON ar koka veida struktūrvienību klasifikatora HTML
     */
    public function getDepartments(Request $request)
    {
        $source_id = $request->input('item_id', 0);
        
        $this->updateEmployeeExistance();
        
        $sql_rel = "SELECT id, parent_id, title, is_employees FROM in_departments WHERE source_id = :source_id ORDER BY title";
                        
        DB::setFetchMode(PDO::FETCH_ASSOC); // We need to get values as array to use it in recursion                  

        $rows = DB::select($sql_rel, array('source_id' => $source_id));                 

        DB::setFetchMode(PDO::FETCH_CLASS); // Set back default fetch mode
        
        return response()->json([
            'success' => 1, 
            'html' => view('pages.department_tree', ['tree' => $this->generateTree($rows), 'node_count' => count($rows)])->render()
        ]);
    }
    
    /**
     * Updates mark if exists at least 1 employee for departments
     */
    private function updateEmployeeExistance() {
        DB::table('in_departments as dep')
        ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from(Config::get('dx.empl_table'))
                      ->whereRaw('department_id = dep.id');
            })
        ->update(['is_employees' => 1]);
                
        DB::table('in_departments as dep')
        ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from(Config::get('dx.empl_table'))
                      ->whereRaw('department_id = dep.id');
            })
        ->update(['is_employees' => 0]); 
    }
    
    /**
     * Rekursīva funkcija uzzīmē struktūrvienību koku
     * 
     * @param Array $datas      Masīvs ar struktūrvienībām
     * @param integer $parent   Vecāka ID
     * @param integer $depth    Rekursijas iterācija
     * @param string $full_path Izveido struktūrvienības pilno ceļu
     * @return string   HTML ar struktūrvienības koku
     */
    private function generateTree($datas, $parent = 0, $depth=0, $full_path = "")
    {
        if ($depth > 1000) {
            return ''; // Make sure not to have an endless recursion
        }
            
        $tree = '<ul>';

        if (strlen($full_path) > 0)
        {
            $full_path .= "->";
        }

        for($i=0, $ni=count($datas); $i < $ni; $i++){
            if($datas[$i]['parent_id'] == $parent){                    

                $node_path = $full_path . $datas[$i]['title'];
                
                $tree .= view('pages.department_node', [
                    'node_path' => $node_path, 
                    'node_id' => $datas[$i]['id'], 
                    'node_title' => $datas[$i]['title'],
                    'node_children' => $this->generateTree($datas, $datas[$i]['id'], $depth+1, $node_path),
                    'is_disabled' => ($datas[$i]['is_employees'] ? 'false' : 'true')
                ])->render();                    
            }
        }
        $tree .= '</ul>';
        return $tree;
    }
}
