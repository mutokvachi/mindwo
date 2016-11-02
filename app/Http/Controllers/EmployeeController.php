<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DB;
use Webpatser\Uuid\Uuid;
use Config;
use Log;
use App\Libraries\Rights;

/**
 *
 * Darbinieku meklēšanas kontrolieris
 * Nodrošina darbinieku meklēšanu pēc lietotāja ievadītajiem meklēšanas kritērijiem
 * Kā arī nodrošina datu atlasi pēc meklēšanas parametriem, kurus izsauc nospiežot saites, piemēram, tiešā vadītāja saiti
 *
 */
class EmployeeController extends Controller
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
     * Meklēšanas kritērijs - datu avots, priekš lapotāja
     * 
     * @var integer 
     */
    private $source_id_pg = 0;

    /**
     * Meklēšanas kritērijs - departaments
     *  
     * @var string 
     */
    private $department = "";
    
    /**
     * Meklēšanas kritērijs - departamenta ID
     * @var integer 
     */
    private $department_id = 0;

    /**
     * Meklēšanas kritērijs - departaments, priekš lapotāja
     *  
     * @var string 
     */
    private $department_pg = "";

    /**
     * Meklēšanas kritērijs - amats
     * 
     * @var string 
     */
    private $position = "";

    /**
     * Meklēšanas kritērijs - tālrunis
     * 
     * @var string 
     */
    private $phone = "";

    /**
     * Meklēšanas parametrs - tiešā vadītāja ID (no tabulas em)
     * 
     * @var integer 
     */
    private $manager_id = 0;

    /**
     * Meklēšanas parametrs - kabineta numurs
     * 
     * @var string
     */
    private $cabinet = "";

    /**
     * Meklēšanas parametrs - kabineta adrese
     * 
     * @var string 
     */
    private $office_address = "";

    /**
     * Meklēšanas parametrs - aizvietotāja darbinieka ID (no tabulas em)
     * 
     * @var integer
     */
    private $subst_empl_id = 0;

    /**
     * Pazīme, vai pieprasījums izpildīts klikšķinot uz darbinieka informācijas bloka saites
     * 
     * @var type 
     */
    private $is_from_link = 0;

    /**
     * Pazīme, vai meklēšana tiek veikta no augšējā teksta lauka (dinamiskā meklēšana) AJAX pieprasījuma
     * @var integer
     */
    private $is_fast_search = 0;
    
    /**
     * Darbinieku meklēšana
     * 
     * @param       Request $request GET/POST pieprasījuma objekts
     * @return      Response         HTML lapa
     */
    public function searchEmployee(Request $request)
    {

        $this->setParams($request);

        $employees = $this->getEmployees();

        $total_count = $employees->count();

        $this->setManagerOrder($employees, $request);

        $employees = $employees->orderBy('em.' . Config::get('dx.empl_fields.empl_name'));

        $employees = $employees->paginate(Config::get('dx.feeds_page_rows_count'));

        $sources = DB::table('in_sources')->get();

        $this->clearFromLinkParams();
        
        $empl_list_rights = Rights::getRightsOnList(Config::get('dx.employee_list_id'));
        $is_list_rights = 0;
        if ($empl_list_rights) {
            $is_list_rights = $empl_list_rights->is_edit_rights;
        }
        
        return view('pages.employees', [
            'employees' => $employees,
            'page_title' => "Meklēšanas rezultāti",
            'block_guid' => Uuid::generate(4),
            'avatar' => get_portal_config('EMPLOYEE_AVATAR'),
            'source_icon' => get_portal_config('DATASOURCE_ICON_CLASS'),
            'criteria' => $this->criteria,
            'department' => $this->department,
            'department_id' => $this->department_id,
            'position' => $this->position,
            'phone' => $this->phone,
            'source_id' => $this->source_id,
            'manager_id' => $this->manager_id,
            'total_count' => $total_count,
            'sources' => $sources,
            'subst_empl_id' => $this->subst_empl_id,
            'cabinet' => $this->cabinet,
            'office_address' => $this->office_address,
            'click2call_url' => get_portal_config('CLICK2CALL_URL'),
            'fixed_phone_part' => get_portal_config('CLICK2CALL_INNER_PHONE'),
            'source_id_pg' => $this->source_id_pg,
            'department_pg' => $this->department_pg,
            'is_from_link' => $this->is_from_link,
            'profile_url' => Config::get('dx.employee_profile_page_url'),
            'is_list_rights' => $is_list_rights
        ]);
    }

    /**
     * Darbinieku meklēšana ar AJAX pieprasījumu
     * 
     * @param       Request $request POST AJAX pieprasījuma objekts
     * @return      Response         HTML lapa
     */
    public function searchAjaxEmployee(Request $request)
    {
        $this->is_fast_search = 1;
        $this->setParams($request);

        $employees = $this->getEmployees();

        $total_count = $employees->count();

        $this->setManagerOrder($employees, $request);

        $employees = $employees->orderBy('em.' . Config::get('dx.empl_fields.empl_name'))
                ->take(Config::get('dx.ajax_employees_count'));

        $employees = $employees->get();

        $sources = DB::table('in_sources')->where('is_for_search', '=', 1)->get();
        
        $empl_list_rights = Rights::getRightsOnList(Config::get('dx.employee_list_id'));
        $is_list_rights = 0;
        if ($empl_list_rights) {
            $is_list_rights = $empl_list_rights->is_edit_rights;
        }
        
        $html = view('pages.employees_ajax', [
            'employees' => $employees,
            'avatar' => get_portal_config('EMPLOYEE_AVATAR'),
            'click2call_url' => get_portal_config('CLICK2CALL_URL'),
            'fixed_phone_part' => get_portal_config('CLICK2CALL_INNER_PHONE'),
            'no_source_icon' => true,
            'profile_url' => Config::get('dx.employee_profile_page_url'),
            'is_list_rights' => $is_list_rights
        ])->render();

        return response()->json(['success' => 1, 'html' => $html]);
    }

    /**
     * Ja pieprasījums notika no darbinieku informācijas bloka saites, tad jānotīra departaments un datu avots - lai tie nerādās meklēšanas rīkā
     */
    private function clearFromLinkParams()
    {
        if ($this->is_from_link) {
            $this->department = "";
            $this->source_id = 0;
        }
    }

    /**
     * Uzstāda kārtošanas kritēriju, ja meklēšanas tiek veikta pēc tiešā vadītāja
     * Nodrošina, ka tiešais vadītājs vienmēr ir kā pirmais
     * 
     * @param Object $employees Darbinieku datu bāzes pieprasījuma objekts
     */
    private function setManagerOrder(&$employees, $request)
    {
        if ($this->manager_id > 0) {
            // Uzmnaību: potenciāla SQL injekcija, tāpēc parametrs manager_id tiek validēts metodē setParams
            $employees = $employees->orderBy(DB::raw("case when em.id = " . $this->manager_id . " then 0 else 1 end"));
        }
    }

    /**
     * Uzstāda meklēšanas kritērijus
     * 
     * @param $request POST pieprasījuma objekts
     */
    private function setParams(Request $request)
    {
        $this->criteria = trim($request->input('criteria', ''));
        $this->source_id = $request->input('source_id', 0);
        $this->department = trim($request->input('department', ''));
        $this->position = $request->input('position', '');
        $this->phone = $request->input('phone', '');
        $this->manager_id = $request->input('manager_id', 0);
        $this->cabinet = $request->input('cabinet', '');
        $this->office_address = $request->input('office_address', '');
        $this->subst_empl_id = $request->input('subst_empl_id', 0);
        $this->is_from_link = $request->input('is_from_link', 0);
        $this->department_id = $request->input('department_id', 0);
        
        //$this->setPhoneParam();
        $this->setReverseParam();

        if ($this->manager_id != 0) {
            if (!is_numeric($this->manager_id)) {
                $this->manager_id = DB::table('em')->max('id') + 1; // lai nevienu neatrod
            }
        }

        // lapotāja parametri, netiks nekadā veidā notīrīti
        $this->source_id_pg = $this->source_id;
        $this->department_pg = $this->department;
    }

    /**
     * Ja meklēšanas kritērijs ir skaitlis, tad uzstāda to kā telefona numura kritēriju
     */
    private function setPhoneParam()
    {
        if (is_numeric($this->criteria)) {
            $this->phone = $this->criteria;
            $this->criteria = "";
        }
    }

    /**
     * Ja kritērijā ir tukšums starp vārdiem, tad izveido papildus kritēriju samainot vārdus vietām
     * Tas nepieciešams, lai meklētu gan pēc "Vārds Uzvārds", gan "Uzvārds Vārds"
     */
    private function setReverseParam()
    {
        $name_arr = explode(" ", $this->criteria);
        $this->criteria2 = "";

        if (count($name_arr) > 1) {
            $this->criteria2 = $name_arr[1] . "%" . $name_arr[0];
        }
    }

    /**
     * Sagatavo SQL pieprasījuma objektu ar meklēšanas kritērijiem
     * 
     * @return Object
     */
    private function getEmployees()
    {
        $employees = DB::table(Config::get('dx.empl_table') . ' as em')
                ->select(DB::raw('
                                em.birth_date,
                                em.picture_guid,
                                em.' . Config::get('dx.empl_fields.empl_name') . ' as employee_name,
                                em.' . Config::get('dx.empl_fields.empl_position') . ' as position,
                                em.email,
                                em.source_id,
                                em.phone,
                                em.department_id,
                                em.office_address,
                                em.manager_id,
                                em.office_cabinet,
                                em.left_to,
                                em.substit_empl_id,
                                in_sources.title as source_title, 
                                ifnull(in_sources.feed_color,"#f1f4f6") as feed_color,
                                in_sources.icon_class as source_icon,
                                case when day(em.birth_date) = day(now()) and month(em.birth_date) = month(now()) then 1 else 0 end as is_today,
                                man.' . Config::get('dx.empl_fields.empl_name') . ' as manager_name,
                                le.title as left_reason,
                                case when now() between em.left_from and em.left_to then em.left_to else null end as left_to_date,
                                subst.' . Config::get('dx.empl_fields.empl_name') . ' as subst_empl_name,
                                in_departments.title as department,
                                em.id
                                '))
                ->leftJoin('in_sources', 'em.source_id', '=', 'in_sources.id')
                ->leftJoin('in_departments', 'em.department_id', '=', 'in_departments.id')
                ->leftJoin(Config::get('dx.empl_table') . ' as man', 'em.manager_id', '=', 'man.id')
                ->leftJoin('in_left_reasons as le', 'em.left_reason_id', '=', 'le.id')
                ->leftJoin(Config::get('dx.empl_table') . ' as subst', 'em.substit_empl_id', '=', 'subst.id');

        $this->setWhere($employees);
        $employees->whereNotIn('em.id', Config::get('dx.empl_ignore_ids'));
        
        return $employees;
    }

    /**
     * Uzstāda Where nosacījumus datu bāzes objektam
     * 
     * @param Object $employees Darbinieku datu bāzes pieprasījuma objekts
     */
    private function setWhere(&$employees)
    {
        $this->whereCabinet($employees);
        $this->whereSource($employees);
        $this->whereCriteria($employees);
        $this->whereDepartment($employees);
        $this->wherePosition($employees);
        $this->wherePhone($employees);
        $this->whereManager($employees);
        $this->whereSubstitute($employees);

        $employees->whereNull('em.' . Config::get('dx.empl_fields.empl_end_date')); // Tikai aktuālos darbiniekus
    }

    /**
     * Pievieno datu bāzes pieprasījumam kabineta/adreses nosacījumus
     * 
     * @param Object $employees Darbinieku datu bāzes pieprasījuma objekts
     */
    private function whereCabinet(&$employees)
    {
        if (strlen($this->cabinet) > 0) {
            $employees->where('em.office_address', '=', $this->office_address)
                    ->where('em.office_cabinet', '=', $this->cabinet);
        }
    }

    /**
     * Pievieno datu bāzes pieprasījumam datu avota nosacījumu
     * 
     * @param Object $employees Darbinieku datu bāzes pieprasījuma objekts
     */
    private function whereSource(&$employees)
    {
        if ($this->source_id > 0)
        {
            $employees->where('em.source_id', '=', $this->source_id);
        }
    }

    /**
     * Pievieno datu bāzes pieprasījumam meklēšanas frāzes nosacījumu (arī reverso, lai meklētu pēc Uzvārds/Vārds kombinācijām)
     * 
     * @param Object $employees Darbinieku datu bāzes pieprasījuma objekts
     */
    private function whereCriteria(&$employees)
    {
        if (strlen($this->criteria) > 0) {
            $employees->where(function($query)
            {

                $query->where('em.' . Config::get('dx.empl_fields.empl_name'), 'like', '%' . $this->criteria . '%');

                if (strlen($this->criteria2) > 0) {
                    $query = $query->orWhere('em.' . Config::get('dx.empl_fields.empl_name'), 'like', '%' . $this->criteria2 . '%');
                }

                if ($this->is_fast_search == 0) {
                    $query = $query->orWhere('em.' . Config::get('dx.empl_fields.empl_position'), 'like', '%' . $this->criteria . '%');
                    $query = $query->orWhere('em.email', 'like', '%' . $this->criteria . '%');
                }
                
                $query = $query->orWhere('em.phone', 'like', '%' . $this->criteria . '%');

                $query = $query->orWhere('em.office_cabinet', 'like', '%' . $this->criteria . '%');
                
                
            });
        }
    }

    /**
     * Pievieno datu bāzes pieprasījumam departamenta nosacījumu
     * 
     * @param Object $employees Darbinieku datu bāzes pieprasījuma objekts
     */
    private function whereDepartment(&$employees)
    {
        if ($this->department_id == 0) {
            if (strlen($this->department) > 0) {
                $employees->where('in_departments.title', 'like', '%' . $this->department . '%');
            }
        }
        else {
            $employees->where('em.department_id', '=', $this->department_id);
        }
    }

    /**
     * Pievieno datu bāzes pieprasījumam amata nosacījumu
     * 
     * @param Object $employees Darbinieku datu bāzes pieprasījuma objekts
     */
    private function wherePosition(&$employees)
    {
        if (strlen($this->position) > 0) {
            $employees->where('em.' . Config::get('dx.empl_fields.empl_position'), 'like', '%' . $this->position . '%');
        }
    }

    /**
     * Pievieno datu bāzes pieprasījumam tālruņa numura nosacījumu
     * 
     * @param Object $employees Darbinieku datu bāzes pieprasījuma objekts
     */
    private function wherePhone(&$employees)
    {
        if (strlen($this->phone) > 0) {
            $employees->where('em.phone', 'like', '%' . $this->phone . '%');
        }
    }

    /**
     * Pievieno datu bāzes pieprasījumam tiešā vadītāja nosacījumu
     * 
     * @param Object $employees Darbinieku datu bāzes pieprasījuma objekts
     */
    private function whereManager(&$employees)
    {
        if ($this->manager_id > 0) {
            $employees->where(function($query)
            {

                $query->where('em.manager_id', '=', $this->manager_id)
                        ->orWhere('em.id', '=', $this->manager_id);
            });
        }
    }

    /**
     * Pievieno datu bāzes pieprasījumam aizvietotāja nosacījumu
     * 
     * @param Object $employees Darbinieku datu bāzes pieprasījuma objekts
     */
    private function whereSubstitute(&$employees)
    {
        if ($this->subst_empl_id > 0) {
            $employees->where('em.id', '=', $this->subst_empl_id);
        }
    }

}
