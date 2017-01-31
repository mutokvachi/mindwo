<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Webpatser\Uuid\Uuid;
use App\Libraries\Rights;
use App\Libraries\FieldsHtm;
use App\Libraries\DataView;
use App\Exceptions;
use Auth;
use DB;
use Config;
use Hash;
use Log;
use Sunra\PhpSimple\HtmlDomParser;

class VisualWFController extends Controller
{
    private $xml_cell_list = [];
    private $xml_cell_levels = [];
    private $current_x = 20;
    private $current_y = 20;
    private $workflow_id = 0;
    private $arrow_counter = 0;

    public function test()
    {
        return view('pages.wf_test', ['json_data' => '',
                    'portal_name' => 'Mindwo',
                    'form_title' => 'Workflow',
                    'xml_data' => $this->prepareXML(5)])->render();
    }

    public function getFirstStep($workflow_id)
    {
        $workflow = \App\Models\Workflow\Workflow::find($workflow_id);

        if ($workflow) {
            return $workflow->workflowSteps()->orderBy('step_nr', 'ASC')->first();
        } else {
            return null;
        }
    }

    private function prepareXML($workflow_id)
    {
        $this->workflow_id = $workflow_id;

        $workflow_step = $this->getFirstStep($workflow_id);

        $xml = new \SimpleXMLElement('<mxGraphModel />');

        $root = $xml->addChild('root');

        $mxCell = $root->addChild('mxCell');
        $mxCell->addAttribute('id', '0');

        $mxCell = $root->addChild('mxCell');
        $mxCell->addAttribute('id', '1');
        $mxCell->addAttribute('parent', '0');

        $is_first = true;

        $this->createMxCell($root, false, $workflow_step->id, 2, $workflow_step->step_nr, 0, 'ellipse', 20, 20);

        //  $this->createMxCell($root, $workflow_step->step_nr, $workflow_step->yes_step_nr, $workflow_step->no_step_nr, ($workflow_step->task_type_id == 5 ? 'rhombus' : 'rounded'));
        // Removes 'xml version="1.0"' at the beginning of the xml
        $dom = dom_import_simplexml($xml);
        return $dom->ownerDocument->saveXML($dom->ownerDocument->documentElement);
    }

    private function createMxCell(&$root, $value, $step_id, $step_nr, $yes_step_nr, $no_step_nr, $shape, $x, $y)
    {
        // Exit if cell already exist
        if (in_array($step_nr, $this->xml_cell_list)) {
            return;
        }

        $this->xml_cell_list[] = $step_nr;

        $mxCell = $root->addChild('mxCell');

        $mxCell->addAttribute('id', 's' . $step_nr);
        $mxCell->addAttribute('vertex', '1');
        $mxCell->addAttribute('parent', '1');
        $mxCell->addAttribute('workflow_step_id', $step_id);
        $mxCell->addAttribute('style', 'overflow=hidden;html=1;whiteSpace=wrap;shape=' . $shape);

        if ($value) {
            $mxCell->addAttribute('value', htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
        }

        $mxGeometry = $mxCell->addChild('mxGeometry');
        $mxGeometry->addAttribute('as', 'geometry');

        $width = 0;
        $height = 0;

        if ($shape == 'ellipse') {
            $width = '20';
            $height = '20';
        } elseif ($shape == 'rhombus') {
            $width = '100';
            $height = '100';
        } else {
            $width = '100';
            $height = '60';
        }

        $mxGeometry->addAttribute('width', $width);
        $mxGeometry->addAttribute('height', $height);

        $mxGeometry->addAttribute('x', $x);
        $mxGeometry->addAttribute('y', $y);

        // Creates last element
        if ($yes_step_nr == 0 && $shape != 'ellipse') {

            $this->createMxCell($root, false, -1, ($step_nr + 1), 0, 0, 'ellipse', $x, $y + $height + 30);
            $this->createArrow($root, $step_nr, ($step_nr + 1), '');
        } else {
            if ($yes_step_nr > 0) {
                $workflow_step = \App\Models\Workflow\WorkflowStep::where('workflow_def_id', $this->workflow_id)
                        ->where('step_nr', $yes_step_nr)
                        ->first();

                $new_shape = ($workflow_step->task_type_id == 5 ? 'rhombus' : 'rounded');

                $this->createMxCell($root, $workflow_step->step_title, $workflow_step->id, $workflow_step->step_nr, $workflow_step->yes_step_nr, $workflow_step->no_step_nr, $new_shape, $x, $y + $height + 30);

                $arrow_value = '';
                if ($shape == 'rhombus') {
                    $arrow_value = trans('workflow.yes');
                }

                $this->createArrow($root, $step_nr, $workflow_step->step_nr, $arrow_value);
            }

            if ($no_step_nr > 0) {
                $workflow_step = \App\Models\Workflow\WorkflowStep::where('workflow_def_id', $this->workflow_id)
                        ->where('step_nr', $no_step_nr)
                        ->first();

                $new_shape = ($workflow_step->task_type_id == 5 ? 'rhombus' : 'rounded');

                $this->createMxCell($root, $workflow_step->step_title, $workflow_step->id, $workflow_step->step_nr, $workflow_step->yes_step_nr, $workflow_step->no_step_nr, $new_shape, $x + $width + 40, $y + $height + 30);

                $arrow_value = '';
                if ($shape == 'rhombus') {
                    $arrow_value = trans('workflow.no');
                }

                $this->createArrow($root, $step_nr, $workflow_step->step_nr, $arrow_value);
            }
        }
    }

    private function createArrow(&$root, $parent, $child, $value)
    {
        $mxCell = $root->addChild('mxCell');

        // $mxCell->addAttribute('id', 'arrow' . $this->arrow_counter++);
        $mxCell->addAttribute('edge', '1');
        $mxCell->addAttribute('parent', '1');
        $mxCell->addAttribute('source', 's' . $parent);        
        $mxCell->addAttribute('target', 's' . $child);
        // labelBackgroundColor=white
        $mxCell->addAttribute('style', 'fontColor=black;labelPosition=right;align=left;');
        
        if ($value) {
            $mxCell->addAttribute('value', htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
        }

        $mxGeometry = $mxCell->addChild('mxGeometry');
        $mxGeometry->addAttribute('relative', '1');
        $mxGeometry->addAttribute('as', 'geometry');
    }
    
    public function save(Request $request){
        $this->validate($request, [
            'workflow_id' => 'required|integer|exists:dx_workflows_def,id'
        ]);
        
        $workflow_id = $request->input('workflow_id');
        
        $xlm_data = $request->input('xml_data');
        
         $workflow_data = HtmlDomParser::str_get_html($xlm_data);

         $arrow_html = $workflow_data->find('mxCell[source,target]');
         
         
        if ($arrow_html) {
            $html .= '<div style="page-break-after:always;"></div>';
            
            return;
        }
    }

    public function getWFForm(Request $request)
    {
        $this->validate($request, [
            'item_id' => 'required|integer|exists:dx_workflows_def,id'
        ]);

        $workflow_id = $request->input('item_id');

        $grid_htm_id = $request->input('grid_htm_id', '');
        $frm_uniq_id = Uuid::generate(4);
        $is_disabled = 1; //read-only rights by default

        $form_htm = view('workflow.visual_ui.wf_form', [
            'frm_uniq_id' => $frm_uniq_id,
            'form_title' => trans('task_form.form_title'),
            'is_disabled' => $is_disabled,
            'grid_htm_id' => $grid_htm_id,
            'item_id' => $workflow_id,
            'xml_data' => $this->prepareXML($workflow_id)
                ])->render();

        return response()->json(['success' => 1, 'html' => $form_htm]);
    }
}