<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Libraries\Structure;
use DB;

/**
 *
 * Struktūras veidošanas kontrolieris
 *
 *
 * Kontrolieris nodrošina sistēmas reģistru ģenerēšanas funkcionalitāti (reģistri, formas, skati)
 * Laravel db struktūras objektu info: http://www.doctrine-project.org/api/dbal/2.5/class-Doctrine.DBAL.Schema.Column.html
 */
class StructureController extends Controller
{

    /**
     * Izpilda struktūras veidošanas metodi
     *
     * @param   Request     $request     POST/GET pieprasījuma objekts
     * @param   string      $method_name Izpildāmās metodes nosaukums
     * @return  Response    JSON rezultāts
     */
    public function doMethod(Request $request, $method_name)
    {
        $reg = Structure\StructMethodFactory::build_method($method_name);
        $rez = $reg->doMethod();

        return response()->json(['success' => 1, 'rez' => $rez]);
    }

    /**
     * Atgriež struktūras ģenerēšanas metodei atbilstošo HTML formu
     * 
     * @param \Illuminate\Http\Request $request POST/GET pieprasījuma objekts
     * @param string $method_name               Struktūras ģenerēšanas metodes nosaukums
     * @return Response                         JSON rezultāts
     */
    public function getForm(Request $request, $method_name)
    {
        $reg = Structure\StructMethodFactory::build_method($method_name);

        return response()->json(['success' => 1, 'html' => $reg->getFormHTML()]);
    }
    
    /**
     * Ģenerē lietotāja rokasgrāmatu
     * 
     * @param \Illuminate\Http\Request $request
     * @return string HTML rezultāts
     */
    public function generateManual(Request $request) {
        $doc_generator = new Structure\DocGenerator();
        return $doc_generator->generateManual();        
    }
    
    /**
     * Ģemerē sistēmas PPA dokumentāciju
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function generatePPA(Request $request) {        
        $doc_generator = new Structure\DocGenerator();
        return $doc_generator->generatePPA(false); 
    }
    
     /**
     * Ģemerē sistēmas PPA dokumentāciju kā HTML lapu (bez CMS saskarnes)
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function generatePPAHtml(Request $request) {        
        $doc_generator = new Structure\DocGenerator();
        return $doc_generator->generatePPA(true); 
    }
    
    public function generateChangesSQL(Request $request) {
        $events = DB::table('dx_db_events as e')
                 ->join('dx_lists as l', 'e.list_id', '=', 'l.id')
                 ->join('dx_objects as o', 'l.object_id', '=', 'o.id')
                 ->select(DB::raw('e.*, o.db_name as table_name'))
                 ->where('e.type_id', '=', 2) // pagaidam tikai updates
                 ->orderBy('id', 'ASC')->get();
        $sql = "";
        foreach ($events as $event) {
            $data = DB::table('dx_db_history as h')
                    ->join('dx_lists_fields as lf', 'h.field_id', '=', 'lf.id')                   
                    ->select(DB::raw('h.*, lf.db_name, lf.type_id'))
                    ->where('event_id', '=', $event->id)
                    ->get();
            
            $vals = "";
            foreach ($data as $row){                
                if (strlen($vals) > 0) {
                    $vals .= ", ";
                }
                $vals .= $row->db_name . "='" . $row->new_val_txt . "'";
            }
            
            $sql .= ";UPDATE " . $event->table_name . " SET " . $vals . " WHERE id=" . $event->item_id;
        }
        
        return $sql;
    }

}
