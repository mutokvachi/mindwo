<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Exceptions;
use App\Libraries\Rights;
use App\Libraries\DataView;
use DB;

class WordController extends Controller
{ 
    /**
    *
    * Word datņu ģenerēšanas kontrolieris
    *
    *
    * Kontrolieris nodrošina Word datņu ģenerēšanu no sagatavēm.
    * Sagatavēs speciālos simbolos tiek norādīti lauku nosaukumi, kuri tiek aizpildīti ar atbilstošām lauku vērtībām
    * 
    *
    */
    
    /**
     * Ģenerē Word datni. 
     * Request parametrā filter_data (JSON formāts) obligāti jābūt norādītam kritērijam attiecībā uz id lauku - lai varētu atlasīt tieši 1 ierakstu, kuram ģenerēt Word datni
     *
     * @param   Request     $request    POST pieprasījuma objekts, izsauc no AJAX
     * @return  Response                JSON ar word lauka HTML
     */    
    public function generateWord(Request $request)
    {
        // validējam reģistru
        $this->validate($request, [
            'list_id' => 'required|integer|exists:dx_lists,id',
            'item_id' => 'required|integer',
            'filter_data' => 'required'
        ]);
        
        $list_id = $request->input('list_id');
        $item_id = $request->input('item_id');
        
        // pārbaudam vai ir tiesības uz reģistru
        $rights = Rights::getRightsOnList($list_id);
        
        if ($rights == null || $rights->is_edit_rights == 0)
        {
            throw new Exceptions\DXCustomException("Jums nav nepieciešamo tiesību šajā reģistrā!");
        }
        
        // pārbaudam vai ierakstu drīkst rediģēt - tas nav darbplūsmā un nav Apstiprināts
        if (!Rights::getIsEditRightsOnItem($list_id, $item_id))
        {
            throw new Exceptions\DXCustomException("Ierakstu nevar rediģēt, jo tas ir darbplūsmā vai arī ar statusu Apstiprināts!");
        }
        
        // nosakam skatu kurš tiks izmantots Word ģenerēšanai - lauku vērtību iegūšanai
        $view_row = DB::table('dx_views')->where('list_id', '=', $list_id)->where('is_for_word_generating', '=', 1)->first();
        
        if (!$view_row)
        {
            throw new Exceptions\DXCustomException("Reģistram nav nodefinēts neviens skats, kurš paredzēts Word ģenerēšanai!");
        }
        
        $word = DataView\DataViewFactory::build_view('Word', $view_row->id);
        $word->item_id = $item_id;
        
        $htm = $word->getViewHtml();
        
        return response()->json(['success' => 1, 'html' => $htm, 'field_id' => $word->file_field->id]);
    }
}