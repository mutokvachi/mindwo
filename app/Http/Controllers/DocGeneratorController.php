<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Exceptions;
use App\Libraries\Rights;
use App\Libraries\DataView;
use DB;

/**
 * Document generator controller
 *
 * Generates Word or PDF documents from templates.
 * In template must be provided fields in format ${field_name}.
 * Field names are used like in views (not from forms).
 */
class DocGeneratorController extends Controller
{

    /**
     * Generates document file in Word on PDF format
     * 
     * @param   integer $list_id Register ID
     * @param   integer $item_id Register item ID
     * @param   integer $template_id Template ID. If 0 then system will try to use template if there is only 1 template attached to register
     * 
     * @return  \Illuminate\Http\JsonResponse Returns generation status and meta data in JSON format
     */
    public function generateDoc($list_id, $item_id, $template_id)
    {
        $rights = Rights::getRightsOnList($list_id);

        if ($rights == null || $rights->is_edit_rights == 0) {
            throw new Exceptions\DXCustomException(trans('errors.no_rights_on_register'));
        }

        // check workflow status
        if (!Rights::getIsEditRightsOnItem($list_id, $item_id)) {
            throw new Exceptions\DXCustomException(trans('errors.doc_gener_in_workflow'));
        }

        $templates = $this->getTemplate($template_id, $list_id);

        if (count($templates) == 0) {
            throw new Exceptions\DXCustomException(trans('errors.doc_gener_no_template'));
        }

        if (count($templates) > 1) {

            $templ_list_id = \App\Libraries\DBHelper::getListByTable('dx_doc_templates')->id;

            $templ_view_id = DB::table('dx_views')
                             ->where('list_id', '=', $templ_list_id)
                             ->orderBy('is_default', 'DESC')
                             ->first()
                             ->id;

            return response()->json([
                'success' => 1,
                'html' => view('elements.form_template_rows', [
                                    'templates' => $templates,
                                    'templ_list_id' => $templ_list_id                                   
                            ])->render(),
                'field_id' => 0,
                'templ_view_id' => $templ_view_id
            ]);
        }
        
        $doc = new \App\Libraries\DocGenerator($list_id, $item_id, $templates[0]);
        $htm = $doc->updateItem()->getFieldHTM();

        return response()->json([
            'success' => 1, 
            'html' => $htm, 
            'field_id' => $doc->field_row->id
        ]);
    }

    /**
     * Prepares templates array
     *
     * @param integer $template_id Template ID or 0 if no template known jet
     * @param integer $list_id Register ID
     * @return array
     */
    private function getTemplate($template_id, $list_id) {
        if ($template_id) {
            return DB::table('dx_doc_templates')
                    ->where('id', '=', $template_id)
                    ->get();
        }

        return DB::table('dx_doc_templates')
                ->where('list_id', '=', $list_id)
                ->orderBy('title')
                ->get();
    }

}
