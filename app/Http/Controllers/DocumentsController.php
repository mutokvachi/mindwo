<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DB;
use Auth;
use Config;

/**
 * Dokumentu kontrolieris
 * Nodrošina dokumentu meklēšanu un dokumentiem saistīto funkcionalitāti
 */
class DocumentsController extends Controller
{
    /**
     * Meklēšanas kritērijs - frāze
     * 
     * @var string 
     */
    private $criteria = "";

    /**
     * Meklēšanas kritērijs - datu avots
     * 
     * @var integer
     */
    private $source_id = 0;

    /**
     * Meklēšanas kritērijs - dokumenta veids
     * 
     * @var integer 
     */
    private $kind_id = 0;

    /**
     * Meklēšanas kritērijs - datums no
     * 
     * @var string 
     */
    private $date_from = "";

    /**
     * Meklēšanas kritērijs - datums līdz
     * 
     * @var string 
     */
    private $date_to = "";

    /**
     * Dokumentu meklēšana
     * Dokumenti tiek agregēti no dažādiem reģistriem/tabulām vienā tabulā dx_doc_agreg
     * 
     * @param       Request $request        GET/POST pieprasījuma objekts
     * @return      Response                HTML lapa ar atrastajiem dokumentiem
     */
    public function searchDocument(Request $request)
    {
        $this->setParams($request);

        $docs = $this->getDocs();

        $total_count = $docs->count();

        $docs = $docs->orderBy('d.reg_date', 'DESC')
                ->paginate(Config::get('dx.grid_page_rows_count'));
        
        $kinds = DB::table('dx_doc_agreg_kinds as k')
                        ->select(DB::raw("k.id, CONCAT(case when l.list_title is not null then CONCAT(l.list_title, ': ') else '' end, k.title) as title"))
                        ->leftJoin('dx_lists as l', 'k.list_id', '=', 'l.id')
                        ->orderBy('title')
                        ->get();
        
        return view('pages.documents', [
            'docs' => $docs,
            'page_title' => trans('search_top.search_page_title'),
            'criteria' => $this->criteria,
            'source_id' => $this->source_id,
            'kind_id' => $this->kind_id,
            'date_from' => $this->date_from,
            'date_to' => $this->date_to,
            'total_count' => $total_count,
            'sources' => DB::table('in_sources')->get(),
            'kinds' => $kinds
        ]);
    }

    /**
     * Uzstāda meklēšanas kritērijus
     * 
     * @param $request POST pieprasījuma objekts
     */
    private function setParams(Request $request)
    {
        $this->criteria = trim($request->input('criteria', ''));
        $this->source_id = $request->input('source_id', '');
        $this->kind_id = $request->input('kind_id', 0);

        $this->date_from = $request->input('pick_date_from', '');
        $this->date_to = $request->input('pick_date_to', '');
    }
    
    /**
     * Izgūst dokumentu masīvu
     * 
     * @return type
     */
    private function getDocs()
    {
        $docs = DB::table('dx_doc_agreg as d')
                ->select(DB::raw("
                                d.*, 
                                ifnull(kind.title, l.list_title) as kind_title, 
                                sr.title as source_title,
                                sr.feed_color,
                                p.title as person_title,
                                p.id as person_id
                                "))
                ->leftJoin('dx_doc_agreg_kinds as kind', 'd.kind_id', '=', 'kind.id')
                ->leftJoin('dx_lists as l', 'd.list_id', '=', 'l.id')
                ->leftJoin('dx_persons as p', 'd.person1_id', '=', 'p.id')
                ->leftJoin('in_sources as sr', 'd.source_id', '=', 'sr.id');

        $this->setWhere($docs);
        
        return $docs;
    }

    /**
     * Pievieno datu bāzes pieprasījumam meklēšanas frāzes nosacījumu
     * 
     * @param Object $docs Dokumentu datu bāzes pieprasījuma objekts
     */
    private function whereCriteria(&$docs)
    {
        if (strlen($this->criteria) > 0) {
            $docs->where(function($query)
            {
                $query->where('d.reg_nr', 'like', '%' . $this->criteria . '%')
                        ->orWhere('d.reg_nr_client', 'like', '%' . $this->criteria . '%')
                        ->orWhere('d.description', 'like', '%' . $this->criteria . '%')
                        ->orWhere('d.file_text', 'like', '%' . $this->criteria . '%')
                        ->orWhere('p.search_title', 'like', '%' . $this->criteria . '%')
                        ->orWhere('d.employee_title', 'like', '%' . $this->criteria . '%');
                
                if (is_numeric($this->criteria)) {
                    $query->orWhere('d.item_id', '=', $this->criteria);
                }
            });
        }
    }

    /**
     * Pievieno datu bāzes pieprasījumam datu avota nosacījumu
     * 
     * @param Object $docs Dokumentu datu bāzes pieprasījuma objekts
     */
    private function whereSource(&$docs)
    {
        if ($this->source_id > 0) {
            $docs->where('d.source_id', '=', $this->source_id);
        }
    }

    /**
     * Pievieno datu bāzes pieprasījumam dokumenta veida nosacījumu
     * 
     * @param Object $docs Dokumentu datu bāzes pieprasījuma objekts
     */
    private function whereKind(&$docs)
    {
        if ($this->kind_id > 0) {
            $docs->where('d.kind_id', '=', $this->kind_id);
        }
    }
    
    /**
     * Ja lietotājam ir norādīts datu avots, tad ierobežojam attiecīgi piekļuvi dokumentiem
     * @param Object $docs Dokumentu datu bāzes pieprasījuma objekts
     */
    private function whereSourceRights(&$docs) {
        /*
        if (Auth::user()->source_id) {
            $docs->where('d.source_id', '=', Auth::user()->source_id);
        }         
        */
    }
    
    /**
     * Lietotājam pieejami tikai tie dokumenti uz kuru reģistriem ir vismaz skatīšanās tiesības
     * @param Object $docs Dokumentu datu bāzes pieprasījuma objekts
     */
    private function whereListRights(&$docs) {
        $roles = DB::table('dx_users_roles')->where('user_id', '=',  Auth::user()->id)->get();
        
        $arr_lists = array();
        foreach($roles as $role) {
            
            $lists = DB::table('dx_roles_lists')->where('role_id', '=', $role->role_id)->get();
            
            foreach($lists as $list) {               
                if (!array_search($list->list_id, $arr_lists)) {
                    array_push($arr_lists, $list->list_id);
                }
            }
        }
        
        $docs->where(function($query) use ($arr_lists) {
            $query->where(function($q) use ($arr_lists) {
                        if (Auth::user()->source_id) {
                            $q->where('d.source_id', '=', Auth::user()->source_id);
                        }
                        
                        $q->whereIn('d.list_id', $arr_lists);
                    })
                  ->orWhere(function($q) {
                      $q->whereExists(function($qe) {
                          $qe->select('t.id')
                             ->from('dx_tasks as t')
                             ->whereRaw('t.list_id = d.list_id')
                             ->whereRaw('t.item_id = d.item_id')
                             ->where('t.task_employee_id', '=', Auth::user()->id);
                      });
                  });        
        });
        
        
    }

    /**
     * Uzstāda Where nosacījumus datu bāzes objektam
     * 
     * @param Object $docs Dokumentu datu bāzes pieprasījuma objekts
     */
    private function setWhere(&$docs)
    {
        if (strlen($this->date_from) > 0 && strlen($this->date_to) > 0) {
            $docs->whereDate('d.reg_date', '>=', $this->date_from)
                    ->whereDate('d.reg_date', '<=', $this->date_to);
        }

        $this->whereCriteria($docs);
        $this->whereSource($docs);
        $this->whereKind($docs);
        $this->whereSourceRights($docs);
        $this->whereListRights($docs);
    }

}
