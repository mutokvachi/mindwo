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
     * @param Request $request    POST pieprasījuma objekts
     * @return Response           JSON izteiksme ar reģistra HTML un JavaScript
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
        $block_grid->grid->grid_data_htm_id = $request->input('grid_data_htm_id', '');

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
     * @return Response JSON rezultāts
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
     * Izgūst reģistra noklusēto skatu, kas ir atļauts attēlošanai formas sadaļā
     * 
     * @param integer $list_id Reģistra ID
     * @return integer Skata ID
     * @throws Exceptions\DXCustomException
     */
    private function getDefaultTabView($list_id)
    {
        $view_row = DB::table("dx_views")->where("list_id", "=", $list_id)->where("is_hidden_from_tabs", "=", 0)->orderBy('is_default', 'DESC')->first();

        if (!$view_row) {
            throw new Exceptions\DXCustomException("Reģistram ar ID " . $list_id . " nav nodefinēts neviens skats, kuru attēlot formas sadaļā!");
        }

        return $view_row->id;
    }

}
