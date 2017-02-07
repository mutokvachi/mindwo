<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use App\Libraries\Rights;
use App\Libraries\FieldsHtm;
use App\Libraries\DataView;
use Request;

use App\Exceptions;
use Auth;
use DB;
use Config;
use Hash;
use Log;

/**
 * Generates PDF with item information (form's data and related sub-grids data)
 */
class FormPDFController extends FormController
{
    /**
     * Item ID
     * @var integer
     */
    public $item_id = 0;
    
    /**
     * Register ID
     * @var integer 
     */
    public $list_id = 0;
    
    /**
     * Form fields data rows not included in tabs
     * 
     * @var object 
     */
    public $fields_general = null;
    
    /**
     * Forms parameters array
     * 
     * @var array 
     */
    public $params = null;
    
    /**
     * Item data row from register
     * @var object 
     */
    public $data_row = null;
    
    /**
     * Array with forms tabs
     * @var object
     */
    public $tab_rows = null;
    
    /**
     * Array with data tabs HTMLs
     * @var array 
     */
    public $arr_data_tabs = [];
    
    /**
     * Generates PDF and downloads it - with form's data for given item
     * 
     * @param integer $item_id Item ID
     * @param integer $list_id Register ID
     * @return \Illuminate\Http\Response
     */
    public function getPDF($item_id, $list_id)
    {        
        Rights::checkFileRights($item_id, $list_id);
        
        $this->item_id = $item_id;
        $this->list_id = $list_id;        
                
        $this->params = $this->getFormParams($list_id);
               
        $this->data_row = $this->getFormItemDataRow($list_id, $item_id, $this->params);
        $fields = $this->getFormFields($this->params);
        $this->tab_rows = $this->getFormTabs($this->params->form_id);
        $this->arr_data_tabs[0] = []; // html for fields without tab
        
        foreach ($fields as $row) {
            if ($row->db_name == "id") {
                // skip ID field - it's allready included in PDF header
                continue;
            }

            $item_value = $this->getItemValue($row);
            
            $field_htm = FieldsHtm\FieldHtmFactory::build_field($row, $this->item_id, $item_value, $this->list_id, '', 1, false);
            $tab_id = ($row->tab_id) ? $row->tab_id : 0;
            
            if (!isset($this->arr_data_tabs[$tab_id])) {
                $this->arr_data_tabs[$tab_id] = [];
            }
            array_push($this->arr_data_tabs[$tab_id], [
                'fld' => $row,
                'val' => $field_htm->getTxtVal()
            ]);
        }
        
        $snappy = \App::make('snappy.pdf');
        
        // Pages numeration
        $snappy->setOption('footer-right', '[page]/[topage]');
        $snappy->setOption('footer-font-size', '8');
        $snappy->setOption('footer-font-name', 'Open Sans",sans-serif');
        
        $html = view('forms.pdf', ['self' => $this])->render();

        return new Response(
            $snappy->getOutputFromHtml($html),
            200,
            array(
                'Content-Description' => 'File Transfer',
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => 'attachment; filename="' . $this->params->form_title . '_id_' . $item_id . '.pdf"',
                'Expires' => 0,
                'Cache-Control' => 'must-revalidate',
                'Pragma' => 'public',
                'Set-Cookie' => 'fileDownload=true; path=/'
            )
        );
    }
    
    /**
     * Returns GRID HTML
     * @param integer $list_id Register ID
     * @param integer $rel_field_id Related register field ID
     * @return string HTML
     */
    public function getGridHtm($list_id, $rel_field_id) {
        $this->setRequestVal('rel_field_id', $rel_field_id);
        $this->setRequestVal('rel_field_value', $this->item_id);
                
        $view_row = DB::table('dx_views')->where('list_id', '=', $list_id)->where('is_hidden_from_tabs', '=', 0)->orderBy('is_default', 'DESC')->first();

        $view_obj = DataView\DataViewFactory::build_view('Excel', $view_row->id);
        
        $html = $view_obj->getViewHtml();
        
        if (!$view_obj->is_rows) {
            $html = "";
        }
        
        return $html;
    }
    
    /**
     * Re-set REQUEST object values in order to work grid control
     * @param string $key POST key
     * @param string $val POST new value
     */
    private function setRequestVal($key, $val) {
        if (Request::has($key)) {
            Request::replace(array($key => $val));
        }
        else {
            Request::merge(array($key => $val));
        }
    }
    
    /**
     * Preformat field value
     * 
     * @param object $fld Field object
     * @return mixed Formated field value
     */
    private function getItemValue($fld) {
        $item_value = "";
				
        if($this->data_row[$fld->db_name] != null)
        {
                $item_value = $this->data_row[$fld->db_name];
        }
        else
        {
                if($fld->type_sys_name == "int" || $fld->type_sys_name == "bool" || $fld->type_sys_name == "decimal")
                {
                        $item_value = 0;
                }
        }
        
        return $item_value;
    }


}