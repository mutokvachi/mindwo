<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Exceptions;
use App\Libraries\DataView;
use App\Libraries\Blocks;
use App\Libraries\FormSave;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use Config;
use Log;

/**
 *
 * Reģistru kontrolieris
 *
 * Kontrolieris nodrošina reģistru skatu attēlošanu sarakstu veidā.
 * Reģistru skati var tikt attēloti galvenajā logā (tad tiek pārlādēta visa lapa) vai arī formu sadaļās.
 *
 */
class GridController extends Controller
{

    /**
     * Attēlo reģistra skatu pēc norādītā skata ID vai skata URL
     * Katram skatam var nodefinēt unikālu URL, pēc kura sistēma var viennozīmīgi identificēt attēlojamo skatu
     * Skatu URL tiek veidoti formātā /skats_{id} vai /skats_{unikāls nosaukums}
     *
     * @param   Request     $request    GET pieprasījuma objekts
     * @param   mixed       $id         Skata idnetifikators (dx_views lauks id) vai arī unikāls url (dx_views lauks url)
     * @return  Response                HTML lapa ar sarakstu un galvenajām izvēlnēm
     */
    public function showViewPage(Request $request, $id)
    {
        try {
            $block_grid = Blocks\BlockFactory::build_block("OBJ=VIEW|VIEW_ID=" . $id);

            $js_inc = view('pages.page_js_includes', [
                'inc_arr' => $block_grid->js_includes_arr
                    ])->render();

            return view('pages.view', [
                'page_title' => $block_grid->grid_title,
                'page_html' => $block_grid->getHTML(),
                'page_js' => $js_inc . $block_grid->getJS(),
                'page_css' => $block_grid->getCSS()
            ]);
        }catch (Exceptions\DXViewAccessException $e){
            $url = $request->root() . $request->getPathInfo() . ($request->getQueryString() ? ('?' . $request->getQueryString()) : '');
            session(['dx_redirect' => $url]);
            return redirect()->route('login');
        }
    }

    /**
     * Attēlo reģistra skatu no AJAX pieprasījuma izsaukma
     *
     * @param Request $request                  POST pieprasījuma objekts
     * @return \Illuminate\Http\JsonResponse    JSON izteiksme ar reģistra HTML un JavaScript
     */
    public function getGrid(Request $request)
    {
        $this->validate($request, [
            'list_id' => 'required|integer|exists:dx_lists,id'
        ]);

        // Opcionāls skata ID
        $view_id = $request->input('view_id', 0);

        // Opcionāli - šie parametri tiek padoti TAB gridam
        $tab_id = $request->input('tab_id', '');
        $rel_field_id = $request->input('rel_field_id', 0);
        $rel_field_value = $request->input('rel_field_value', 0);
        $rel_field_value2 = $request->input('rel_field_value2', 0);

        if ($rel_field_id > 0 && $rel_field_value == 0) {
            $rel_field_value = $rel_field_value2;
        }

        if (!is_numeric($view_id)) {
            $view_row = getViewRowByID($request->url(), $view_id);

            if ($view_row) {
                $view_id = $view_row->id;
            }
        }

        $tab_prefix = "";
        if (strlen($tab_id) > 0) {
            $tab_prefix = "tab_";

            if ($view_id == 0) {
                $view_id = $this->getDefaultTabView($request->input('list_id'));
            }
        }

        $block_grid = Blocks\BlockFactory::build_block("OBJ=VIEW|VIEW_ID=" . $view_id);
        $block_grid->rel_field_id = $rel_field_id;
        $block_grid->rel_field_value = $rel_field_value;
        $block_grid->form_htm_id = $request->input('form_htm_id', '');
        $block_grid->tab_id = $tab_id;
        $block_grid->tab_prefix = $tab_prefix;
        
        $grid_data_id = $request->input('grid_data_htm_id', '');
        if (strlen($grid_data_id) > 0) {
            $block_grid->grid->grid_data_htm_id = $grid_data_id;
        }
        
        return response()->json(['success' => 1, 'html' => $block_grid->getHTML() . $block_grid->getJS()]);
    }

    /**
     * Eksportē grida datus uz Excel un atgeriež noģenerētu datni uz lejupielādēšanu
     * 
     * @param  Request  $request GET pieprasījums
     * @return Response Excel datne
     */
    public function downloadExcel(Request $request)
    {
        $view_id = $request->input('view_id');

        $view_row = getViewRowByID($request->url(), $view_id);

        $view_obj = DataView\DataViewFactory::build_view('Excel', $view_row->id);

        Excel::create($view_obj->getViewTitle(), function($excel) use ($view_obj)
        {

            // Set the title
            $excel->setTitle('Our new awesome title');

            // Chain the setters
            $excel->setCreator('MEDUS')
                    ->setCompany('MEDUS');

            // Call them separately
            $excel->setDescription('A demonstration to change the file properties');

            $excel->sheet('Dati', function($sheet) use ($view_obj)
            {
                $sheet->loadView('excel.table', ['htm' => $view_obj->getViewHtml()]);
            });
            
        })->download('xlsx', [
            'Set-Cookie'  => 'fileDownload=true; path=/'
        ]);
    }

    /**
     * Dzēš norādītos ierakstus no reģistra
     * 
     * @param \Illuminate\Http\Request $request POST/GET pieprasījuma objekts
     * @return \Illuminate\Http\JsonResponse JSON rezultāts
     */
    public function deleteItems(Request $request)
    {
        $this->validate($request, [
            'list_id' => 'required|integer|exists:dx_lists,id',
            'items' => 'required'
        ]);

        $list_id = $request->input('list_id');

        checkDeleteRights($list_id);

        $items_arr = explode('|', $request->input('items'));

        $form_row = $this->getListForm($list_id);
        $table_row = FormSave::getFormTable($form_row->id);
        $fields = FormSave::getFormsFields(-1, $form_row->id);

        DB::transaction(function () use ($items_arr, $list_id, $table_row, $fields)
        {
            foreach ($items_arr as $item_id) {
                validateRelations($list_id, $item_id);

                \App\Libraries\Helper::deleteItem($table_row, $fields, $item_id);
            }
        });

        return response()->json(['success' => 1]);
    }

    /**
     * Izgūst reģistra datu ievades formas objektu
     * 
     * @param integer $list_id Reģistra id (no tabulas dx_forms)
     * @return Object Formas robjekts
     * @throws Exceptions\DXCustomException
     */
    public static function getListForm($list_id)
    {
        $form_row = DB::table('dx_forms')->where('list_id', '=', $list_id)->first();

        if (!$form_row) {
            throw new Exceptions\DXCustomException("Reģistram ar ID " . $list_id . " nav definēta datu ievades forma.");
        }

        return $form_row;
    }
    
    /**
     * Gets autocompleate field textual value and record count of table for which autocompleate is set
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse JSON data with field meta info
     */
    public function getAutocompleateData(Request $request)
    {
        $this->validate($request, [
            'list_id' => 'required|integer|exists:dx_lists,id',
            'txt_field_id' => 'required|integer|exists:dx_lists_fields,id',
            'value_id' => 'required|integer'
        ]);
        
        $list_id = $request->input('list_id');
        $txt_field_id = $request->input('txt_field_id');        
        $value_id = $request->input('value_id');
        
        $table_item = DB::table('dx_lists')
                ->join('dx_objects', 'dx_lists.object_id', '=', 'dx_objects.id')
                ->select(DB::raw("dx_objects.db_name as table_name, dx_objects.is_multi_registers"))
                ->where('dx_lists.id', '=', $list_id)
                ->first();

        $field_item = DB::table('dx_lists_fields')
                ->select('db_name as rel_field_name', 'is_right_check')
                ->where('id', '=', $txt_field_id)
                ->first();
        
        $data = DB::table($table_item->table_name)
                ->select($field_item->rel_field_name . ' as txt')
                ->where('id', '=', $value_id)
                ->first();
        
        $cnt = DB::table($table_item->table_name)->count();
        Log::info("CUNT: " . $cnt);
        if ($cnt > Config::get('dx.autocompleate_max_count', 20)) {
            $cnt = 3;
            Log::info("SMAL");
        }
        else {
            $cnt = 0;
            Log::info("BIF");
        }
        return response()->json(['success' => 1, 'txt' => $data->txt, 'count' => $cnt]);
    }
    
    /**
     * Gets JSON with view columns re-ordering interface (HTML)
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getViewEditForm(Request $request) {
        $this->validate($request, [
            'view_id' => 'required|integer|exists:dx_views,id'
        ]);
        
        $view_id = $request->input('view_id');
        
        $view = DB::table('dx_views')->where('id', '=', $view_id)->first();              
        
        $list_fields = DB::table('dx_lists_fields as lf')
                        ->leftJoin('dx_field_types as ft', 'lf.type_id', '=', 'ft.id')
                        ->select(
                                'lf.id', 
                                'title_list as title',
                                'ft.sys_name as field_type',
                                'lf.rel_list_id',
                                'lf.rel_display_field_id'
                        )
                        ->where('lf.list_id', '=', $view->list_id)
                        ->whereNotExists(function($query) use ($view_id) {
                            $query->select(DB::raw(1))
                                  ->from('dx_views_fields as vf')
                                  ->where('vf.view_id', '=', $view_id)
                                  ->whereRaw('vf.field_id = lf.id');
                        })                   
                        ->orderBy('lf.title_list')
                        ->get();        
        
        $view_fields = DB::table('dx_views_fields as vf')
                    ->join('dx_lists_fields as lf', 'vf.field_id', '=', 'lf.id')
                    ->leftJoin('dx_field_operations as fo', 'vf.operation_id', '=', 'fo.id')
                    ->leftJoin('dx_field_types as ft', 'lf.type_id', '=', 'ft.id')
                    ->select(
                            'lf.id', 
                            'lf.title_list as title',
                            'vf.operation_id',
                            'fo.title as operation_title',
                            'vf.criteria',
                            'vf.is_hidden',
                            'ft.sys_name as field_type',
                            'lf.rel_list_id',
                            'lf.rel_display_field_id'
                    )
                    ->where('vf.view_id', '=', $view_id)                    
                    ->orderBy('vf.order_index')
                    ->get();        
        
        $htm = view('blocks.view.view_edit_form', [
                'list_fields' => $list_fields,
                'view_fields' => $view_fields,
                'view_title' => $view->title,
                'view_id' => $view_id,
                'list_id' => $view->list_id,
                'is_default' => $view->is_default,
                'is_my_view' => ($view->me_user_id == Auth::user()->id),                
        ])->render();
        
        return response()->json(['success' => 1, 'html' => $htm]);
    }
    
    /**
     * Saves view columns state and some views defaults (available for ordinary users)
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveView(Request $request) {
        $this->validate($request, [ 
            'list_id' => 'required|integer|exists:dx_lists,id',
            'fields' => 'required',
            'view_title' => 'required',
            'grid_id' => 'required'
        ]);        
        
        $view_id = $request->input('view_id', 0); // if 0 then new item
        
        if ($view_id == 0) {
            $view_id = $this->saveNewView($request);
        }
        else {
            $this->updateExistingView($request, $view_id);
        }
        
        // clear grid data from session
        $grid_id = $request->input('grid_id');        
        $request->session()->remove($grid_id . "_view");
        $request->session()->remove($grid_id . "_sql");
        
        return response()->json(['success' => 1, 'view_id' => $view_id]);
    }
    
    /**
     * Deletes view and returns default view ID
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteView(Request $request) {
        $this->validate($request, [ 
            'view_id' => 'required|integer|exists:dx_views,id',
            'list_id' => 'required|integer|exists:dx_lists,id'
        ]);        
        
        $view_id = $request->input('view_id');
        $list_id = $request->input('list_id');
        
        $def_id = $this->getDefaultView($request, $list_id, $view_id);
        
        DB::transaction(function () use ($view_id){
           DB::table('dx_views')->where('id', '=', $view_id)->delete(); 
        });
        
        return response()->json(['success' => 1, 'view_id' => $def_id]);
    }
    
    /**
     * Gets default view ID which will be loaded after another view deletion
     * 
     * @param \Illuminate\Http\Request $request
     * @param integer $list_id List ID
     * @param integer $view_id View ID which will be deleted
     * @return integer View ID
     * @throws Exceptions\DXCustomException
     */
    private function getDefaultView($request, $list_id, $view_id) {
        $fld_is_hidden = 'is_hidden_from_main_grid';
        $tab_id = $request->input('tab_id', '');
        
        if (strlen($tab_id) > 0)
        {
            $fld_is_hidden = 'is_hidden_from_tabs';
        }
        
        $def_view = DB::table('dx_views')
                    ->where('list_id', '=', $list_id)
                    ->where('is_default', '=', 1)
                    ->where($fld_is_hidden, '=', 0)
                    ->where('id', '!=', $view_id)
                    ->first();
        
        if (!$def_view) {
            throw new Exceptions\DXCustomException(trans('errors.cant_delete_default_view'));
        }
        
        return $def_view->id;
    }
    
    /**
     * Save new view with fields and returns view ID
     * 
     * @param \Illuminate\Http\Request $request
     * @return int View ID
     */
    private function saveNewView($request) {        
        
        $json_data = json_decode($request->input('fields'));
        $list_id = $request->input('list_id');
        
        $this->validateTitleUniq($request, "", 0, $list_id);
        
        $id_field_id = DB::table('dx_lists_fields')->where('list_id', '=', $list_id)->where('db_name', '=', 'id')->first()->id;
        $new_view_id = 0;
        
        DB::transaction(function () use ($list_id, $json_data, $request, &$new_view_id, $id_field_id){
            // insert new view
            $new_view_id = DB::table('dx_views')->insertGetId([
                'list_id' => $list_id,
                'title' => $request->input('view_title'), 
                'is_default' => $request->input('is_default', 0),
                'me_user_id' => ($request->input('is_my_view', 0)) ? Auth::user()->id : null,
                'view_type_id' => 1
            ]);
            
            $is_id = false;
            $idx = 0;
            
            // asign fields to view
            foreach($json_data as $item){
                $idx++;
                
                DB::table('dx_views_fields')->insert([
                        'order_index' => $idx*10,
                        'list_id' => $list_id,
                        'view_id' => $new_view_id,
                        'field_id' => $item->field_id,
                        'is_hidden' => $item->is_hidden,
                        'operation_id' => ($item->operation_id > 0) ? $item->operation_id : null,
                        'criteria' => $item->criteria
                ]);
                
                if ($item->field_id == $id_field_id) {
                    $is_id = true;
                }
            }
            
            // add id as hidden field if it was not included in view visible fields
            if (!$is_id) {
                DB::table('dx_views_fields')->insert([                        
                        'list_id' => $list_id,
                        'view_id' => $new_view_id,
                        'field_id' => $id_field_id,
                        'is_hidden' => 1
                ]);
            }
              
        });
        
        return $new_view_id;
    }
    
    /**
     * Update existing view fields
     * 
     * @param \Illuminate\Http\Request $request
     * @param integer $view_id View ID
     */
    private function updateExistingView($request, $view_id) {
                
        $view = DB::table('dx_views')->where('id', '=', $view_id)->first();
        $this->validateTitleUniq($request, $view->title, $view->id, $view->list_id);
        
        $arr = $this->getViewFieldsValArrays($request, $view);
        
        DB::transaction(function () use ($view, $request, $arr, $view_id){
            
            // delete unused fields
            DB::table('dx_views_fields')
            ->where('view_id', '=', $view_id)
            ->whereNotIn('field_id', $arr["arr_all"])
            ->where('field_id', '!=', $arr["id_field_id"]) // ID field is must have
            ->delete();

            // update existing fields vals
            foreach($arr["arr_upd"] as $upd) {
                DB::table('dx_views_fields')
                    ->where('field_id', '=', $upd["field_id"])
                    ->where('view_id', '=', $view_id)
                    ->update($upd["vals"]);
            }            
            
            // insert new fields
            foreach($arr["arr_new"] as $new) {
                DB::table('dx_views_fields')->insert($new["vals"]);
            }
            
            $this->resetDefault($request, $view);            
            
            // update view metadata
            DB::table('dx_views')->where('id', '=', $view_id)->update([
                'title' => $request->input('view_title'), 
                'is_default' => $request->input('is_default', 0),
                'me_user_id' => ($request->input('is_my_view', 0)) ? Auth::user()->id : null
            ]);
            
        });
    }
    
    /**
     * Re-sets default view option
     * 
     * @param \Illuminate\Http\Request $request
     * @param object $view View row (table dx_views)
     * @throws Exceptions\DXCustomException
     */
    private function resetDefault($request, $view) {
        $is_default = $request->input('is_default', 0);
        if ($is_default && !$view->is_default) {
            DB::table('dx_views')
                    ->where('list_id', '=', $view->list_id)
                    ->where('id', '!=', $view->id)
                    ->whereNull('me_user_id')
                    ->update(['is_default' => 0]);
            // update menu last change time - because view can be attached to menu item
            DB::table('in_last_changes')->where('code', '=', 'MENU')->update(['change_time'=>date('Y-n-d H:i:s')]);
        }
    }
    
    /**
     * Validates if new title for view is unique
     * 
     * @param \Illuminate\Http\Request $request
     * @param object $view View row (table dx_views)
     * @throws Exceptions\DXCustomException
     */
    private function validateTitleUniq($request, $old_title, $view_id, $list_id) {
        $title = $request->input('view_title');
        
        if ($title != $old_title) {
            $is_existing = DB::table('dx_views')
                            ->where('list_id', '=', $list_id)
                            ->where('id', '!=', $view_id)
                            ->where('title', '=', $title)
                            ->where(function($query) {
                                $query->whereNull('me_user_id')
                                      ->orWhere('me_user_id', '=', Auth::user()->id);
                            })
                            ->exists();

            if ($is_existing) {
                throw new Exceptions\DXCustomException(trans('errors.duplicate_view_title'));
            }
        }
    }
    
    /**
     * Prepare arrays for update/insert operations - for views columns updating
     * 
     * @param \Illuminate\Http\Request $request
     * @param object $view View row (table dx_view)
     * @return array Data for db update/insert
     */
    private function getViewFieldsValArrays($request, $view) {
        $arr_all = [];
        $arr_upd = [];
        $arr_new = [];
        
        $json_data = json_decode($request->input('fields'));            
        $idx = 0;

        foreach($json_data as $item){
            $idx++;
            $view_field = DB::table('dx_views_fields')
                          ->where('view_id', '=', $view->id)
                          ->where('field_id', '=', $item->field_id)
                          ->first();

            if ($view_field) {
                if ($view_field->order_index != $idx*10 || $view_field->is_hidden != $item->is_hidden || $view_field->operation_id != $item->operation_id || $view_field->criteria != $item->criteria) {
                    
                    array_push($arr_upd, [
                        "field_id" => $item->field_id,
                        "vals" => [
                            'order_index' => $idx*10,
                            'is_hidden' => $item->is_hidden,
                            'operation_id' => ($item->operation_id > 0) ? $item->operation_id : null,
                            'criteria' => $item->criteria
                        ]
                    ]);
                }
            }
            else {
                array_push($arr_new, [
                    "field_id" => $item->field_id,
                    "vals" => [
                        'order_index' => $idx*10,
                        'list_id' => $view->list_id,
                        'view_id' => $view->id,
                        'field_id' => $item->field_id,
                        'is_hidden' => $item->is_hidden,
                        'operation_id' => ($item->operation_id > 0) ? $item->operation_id : null,
                        'criteria' => $item->criteria
                    ]
                ]);
            }

            array_push($arr_all, $item->field_id);
        }
        
        return $this->checkIDField($view, $arr_all, $arr_upd, $arr_new);
    }
    
    /**
     * Check if view have ID field. If not - insert it as hidden field
     * 
     * @param object $view View row (from table dx_views)
     * @param array $arr_all Array with all visible fields
     * @param array $arr_upd Array with fields to be updated
     * @param array $arr_new Array with fields to be inserted
     * @return array Data for db update/insert
     */
    private function checkIDField($view, $arr_all, $arr_upd, $arr_new) {
         
        $id_field_id = DB::table('dx_lists_fields')->where('list_id', '=', $view->list_id)->where('db_name', '=', 'id')->first()->id;
        
        $fld = DB::table('dx_views_fields')->where('view_id', '=', $view->id)->where('field_id', '=', $id_field_id)->first();
        
        if (!$fld) {
            array_push($arr_new, [
                    "field_id" => $id_field_id,
                    "vals" => [
                        'list_id' => $view->list_id,
                        'view_id' => $view->id,
                        'field_id' => $id_field_id,
                        'is_hidden' => 1
                    ]
            ]);
            DB::table('dx_views_fields')->insert(['list_id' => $view->list_id, 'view_id' => $view->id, 'field_id' => $id_field_id, 'is_hidden' => 1]);
        }
        else {
            // check if there is intention to hide ID field in view
            if (!$this->isIDVisible($arr_all, $fld)) {
                array_push($arr_upd, [
                        "field_id" => $fld->field_id,
                        "vals" => [
                            'order_index' => 0,
                            'is_hidden' => 1
                        ]
                    ]);
            }            
        }
        
        return [
            "arr_all" => $arr_all,
            "arr_upd" => $arr_upd,
            "arr_new" => $arr_new,
            "id_field_id" => $id_field_id
        ];
    }
    
    /**
     * Checks if ID field is included in views visible columns
     * 
     * @param array $arr_all All visible columns array
     * @param object $fld ID field row (database table dx_views_fields)
     * @return boolean True - ID field is included, othervise false
     */
    private function isIDVisible($arr_all, $fld) {
        foreach($arr_all as $fld_id) {
            if ($fld_id == $fld->field_id) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Izgūst reģistra noklusēto skatu, kas ir atļauts attēlošanai formas sadaļā
     * 
     * @param integer $list_id Reģistra ID
     * @return integer Skata ID
     * @throws Exceptions\DXCustomException
     */
    private function getDefaultTabView($list_id)
    {
        $view_row = DB::table("dx_views")
                    ->where("list_id", "=", $list_id)
                    ->where("is_hidden_from_tabs", "=", 0)
                    ->where(function($query) {
                        $query->whereNull('me_user_id')
                              ->orWhere('me_user_id', '=', Auth::user()->id);
                    })
                    ->orderBy('is_default', 'DESC')
                    ->orderBy('me_user_id', 'DESC')
                    ->first();

        if (!$view_row) {
            throw new Exceptions\DXCustomException("Reģistram ar ID " . $list_id . " nav nodefinēts neviens skats, kuru attēlot formas sadaļā!");
        }

        return $view_row->id;
    }

}
