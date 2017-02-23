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
    private $error_stack = [];

    public function test()
    {
        $workflow_id = 0;

        $workflow = \App\Models\Workflow\Workflow::find($workflow_id);

        $wf_register_id = 60; //$workflow->list_id;

        /*
          'is_disabled' => $is_disabled,
          'grid_htm_id' => $grid_htm_id,
          'item_id' => $workflow_id,
          'wf_register_id' => $wf_register_id,
          'wf_register_name' => $list_title,
          'wf_data' => $workflow,
          'xml_data' => $this->prepareXML($workflow_id)
         */

        $xml_data = ''; //$this->prepareXML($workflow_id);

        return view('pages.wf_test', ['json_data' => '',
                    'portal_name' => 'Mindwo',
                    'form_title' => 'Workflow',
                    'item_id' => $workflow_id,
                    'wf_register_id' => $wf_register_id,
                    'is_disabled' => false,
                    'wf_register_id' => $wf_register_id,
                    'wf_register_name' => 'Laba liste',
                    'workflow' => $workflow,
                    'xml_data' => $xml_data])->render();
    }

    public function getFirstStep($workflow)
    {
        if ($workflow) {
            return $workflow->workflowSteps()->orderBy('step_nr', 'ASC')->first();
        } else {
            return null;
        }
    }

    private function prepareXML($workflow_id)
    {
        $workflow = \App\Models\Workflow\Workflow::find($workflow_id);

        if (!$workflow) {
            return '';
        }

        if ($workflow->visual_xml) {
            // Varbūt pirms notiek saglabāšana varam iziet visiem elmentiem cauti un izpildīt encode, lai salabo ielādētās pēdiņas.
            //return $workflow->visual_xml;
        }

        $this->workflow_id = $workflow_id;

        $workflow_step = $this->getFirstStep($workflow);

        if (!$workflow_step) {
            return;
        }

        $xml = new \SimpleXMLElement('<mxGraphModel />');

        $root = $xml->addChild('root');

        $mxCell = $root->addChild('mxCell');
        $mxCell->addAttribute('id', '0');

        $mxCell = $root->addChild('mxCell');
        $mxCell->addAttribute('id', '1');
        $mxCell->addAttribute('parent', '0');

        $this->createMxCell($root, false, -1, 2, $workflow_step->step_nr, 0, 'ENDPOINT', 30, 30);

        //  $this->createMxCell($root, $workflow_step->step_nr, $workflow_step->yes_step_nr, $workflow_step->no_step_nr, ($workflow_step->task_type_id == 5 ? 'rhombus' : 'rounded'));
        // Removes 'xml version="1.0"' at the beginning of the xml
        $dom = dom_import_simplexml($xml);
        return $dom->ownerDocument->saveXML($dom->ownerDocument->documentElement);
    }

    private function createMxCell(&$root, $value, $step_id, $step_nr, $yes_step_nr, $no_step_nr, $type_code, $x, $y)
    {
        // Exit if cell already exist
        if (in_array($step_nr, $this->xml_cell_list)) {
            return;
        }

        $this->xml_cell_list[] = $step_nr;

        $mxCell = $root->addChild('mxCell');

        $has_arrow_labels = 0;

        if ($type_code == 'CRIT' || $type_code == 'CRITM') {
            $has_arrow_labels = 1;
            $arrow_count = 2;
            $shape = 'rhombus';
        } else if ($type_code == 'ENDPOINT') {
            $arrow_count = 1;
            $shape = 'ellipse';
        } elseif ($type_code == 'SET') {
            $arrow_count = 1;
            $shape = 'rounded';
        } else {
            $arrow_count = 2;
            $shape = 'rounded';
        }

        $mxCell->addAttribute('id', 's' . $step_nr);
        $mxCell->addAttribute('vertex', '1');
        $mxCell->addAttribute('parent', '1');
        $mxCell->addAttribute('workflow_step_id', $step_id);
        $mxCell->addAttribute('type_code', $type_code);
        $mxCell->addAttribute('has_arrow_labels', $has_arrow_labels);
        $mxCell->addAttribute('arrow_count', $arrow_count);

        $mxCell->addAttribute('style', 'fillColor=#E1E5EC;strokeColor=#4B77BE;fontColor=black;html=1;whiteSpace=wrap;shape=' . $shape);

        if ($value) {
            $mxCell->addAttribute('value', htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
        }

        $mxGeometry = $mxCell->addChild('mxGeometry');
        $mxGeometry->addAttribute('as', 'geometry');

        $width = 0;
        $height = 0;

        if ($shape == 'ellipse') {
            $width = '30';
            $height = '30';
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
        if ((!$yes_step_nr || $yes_step_nr <= 0) && $type_code != 'ENDPOINT') {
            $this->createMxCell($root, false, -1, ($step_nr + 1), 0, 0, 'ENDPOINT', $x, $y + $height + 30);
            $this->createArrow($root, $step_nr, ($step_nr + 1), '', true);
        } else {
            $this->createNextCell($root, $yes_step_nr, $x, $y + $height + 30, $shape, $step_nr, true);

            $this->createNextCell($root, $no_step_nr, $x + $width + 40, $y + $height + 30, $shape, $step_nr, false);
        }
    }

    private function createNextCell($root, $next_step_nr, $x, $y, $parent_shape, $parent_step_nr, $is_yes_arrrow)
    {
        if ($next_step_nr > 0) {
            $workflow_step = \App\Models\Workflow\WorkflowStep::where('workflow_def_id', $this->workflow_id)
                    ->where('step_nr', $next_step_nr)
                    ->first();

            $task_type = $workflow_step->taskType;
            $code = '';
            if ($task_type) {
                $code = $task_type->code;
            }

            $this->createMxCell($root, $workflow_step->step_title, $workflow_step->id, $workflow_step->step_nr, $workflow_step->yes_step_nr, $workflow_step->no_step_nr, $code, $x, $y);

            $arrow_text = '';
            if ($parent_shape == 'rhombus') {
                $arrow_text = $is_yes_arrrow ? trans('workflow.yes') : trans('workflow.no');
            }

            $this->createArrow($root, $parent_step_nr, $workflow_step->step_nr, $arrow_text, $is_yes_arrrow);
        }
    }

    private function createArrow(&$root, $parent, $child, $value, $is_yes_arrrow)
    {
        $mxCell = $root->addChild('mxCell');

        // $mxCell->addAttribute('id', 'arrow' . $this->arrow_counter++);
        $mxCell->addAttribute('edge', '1');
        $mxCell->addAttribute('parent', '1');
        $mxCell->addAttribute('source', 's' . $parent);
        $mxCell->addAttribute('target', 's' . $child);
        // labelBackgroundColor=white
        $mxCell->addAttribute('style', 'fillColor=#E1E5EC;strokeColor=#4B77BE;fontColor=black;labelPosition=right;align=left;');
        $mxCell->addAttribute('is_yes', $is_yes_arrrow ? '1' : '0');

        if ($value) {
            $mxCell->addAttribute('value', htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
        }

        $mxGeometry = $mxCell->addChild('mxGeometry');
        $mxGeometry->addAttribute('relative', '1');
        $mxGeometry->addAttribute('as', 'geometry');
    }

    public function save(Request $request)
    {
        $this->error_stack = [];

        $this->validate($request, [
            'workflow_id' => 'required',
        ]);

        $workflow_id = $request->input('workflow_id');

        if ($workflow_id && $workflow_id > 0) {
            $workflow = \App\Models\Workflow\Workflow::find($workflow_id);
        } else {
            $workflow = new \App\Models\Workflow\Workflow();
        }

        $xlm_data = $request->input('xml_data');

        $workflow->list_id = $request->input('list_id');
        $workflow->title = $request->input('title');
        $workflow->description = $request->input('description');
        $workflow->is_custom_approve = $request->input('is_custom_approve');

        $date_format = config('dx.txt_date_format');
        $valid_to = $request->input('valid_to');
        if ($valid_to && strlen(trim($valid_to)) > 0) {
            $workflow->valid_to = date_create_from_format($date_format, $request->input('valid_to'));
        }

        $valid_from = $request->input('valid_from');
        if ($valid_from && strlen(trim($valid_from)) > 0) {
            $workflow->valid_from = date_create_from_format($date_format, $request->input('valid_from'));
        }

        if ($xlm_data && strlen(trim($xlm_data)) > 0) {
            $workflow->visual_xml = $xlm_data;
            $xml = HtmlDomParser::str_get_html($workflow->visual_xml);

            $this->validateEndpoints($xml);

            $this->validateStepsConnections($workflow, $xml);

            if (count($this->error_stack) > 0) {
                return response()->json(['success' => 0, 'errors' => $this->error_stack]);
            }

            $this->saveRelations($workflow, $xml);
        }

        $workflow->save();

        return response()->json(['success' => 1, 'html' => $workflow->id]);
    }

    private function validateEndpoints($xml)
    {
        $end_points = $xml->find('mxCell[type_code=ENDPOINT]');

        $finish_points_ids = [];
        $start_points_ids = [];

        foreach ($end_points as $end_point) {
            // Finds if end point is finish point - it is finish point if there are arrows going into it
            $finish = $xml->find('mxCell[target=' . $end_point->id . ']');

            if ($finish) {
                $finish_starts = $xml->find('mxCell[source=' . $end_point->id . ']');

                // If finish point has childs then error given because end point can only start workflow or end it - it can not be in the middle of workflow!
                if ($finish_starts) {
                    $this->error_stack[] = trans('errors.workflow.end_point_in_middle');
                } else {
                    $finish_points_ids[] = $end_point->id;
                }
            } else {
                $start = $xml->find('mxCell[source=' . $end_point->id . ']');

                if ($start) {
                    $start_points_ids[] = $end_point->id;
                }
            }
        }

        if (count($start_points_ids) > 1) {
            $this->error_stack[] = trans('errors.workflow.multiple_starting_points');
        }

        if (count($start_points_ids) <= 0) {
            $this->error_stack[] = trans('errors.workflow.no_starting_points');
        }

        if (count($finish_points_ids) <= 0) {
            $this->error_stack[] = trans('errors.workflow.no_finish_points');
        }
    }

    private function validateStepsConnections($workflow, $xml)
    {
        $steps_cells = $xml->find('mxCell[workflow_step_id]');

        $step_not_connected = false;
        $step_dont_have_child = false;

        foreach ($steps_cells as $steps_cell) {
            $step = \App\Models\Workflow\WorkflowStep::find($steps_cell->workflow_step_id);

            if ($step && count($step) > 0) {
                $arrow = $xml->find('mxCell[target=' . $steps_cell->id . ']');

                // If arrow not found then workflow's step has no origin step which means it is not connected to anything
                // Even tho it is be possible that it has childs which ar connected to it
                if (!$arrow) {
                    $step_not_connected = true;
                }

                $arrow = $xml->find('mxCell[source=' . $steps_cell->id . ']');

                // If arrow not found then workflow's step has no origin step which means it is not connected to anything
                // Even tho it is be possible that it has childs which ar connected to it
                if (!$arrow) {
                    $step_dont_have_child = true;
                }
            }

            if ($step_not_connected && $step_dont_have_child) {
                break;
            }
        }

        if ($step_not_connected) {
            $this->error_stack[] = trans('errors.workflow.step_not_connected');
        }

        if ($step_dont_have_child) {
            $this->error_stack[] = trans('errors.workflow.step_dont_have_child');
        }
    }

    private function saveRelations($workflow, $xml)
    {
        $arrows = $xml->find('mxCell[is_yes]');

        foreach ($arrows as $arrow) {
            $source_step_nr = substr($arrow->source, 1);
            $target_step_nr = substr($arrow->target, 1);

            $step = \App\Models\Workflow\WorkflowStep::where('workflow_def_id', $workflow->id)
                    ->where('step_nr', $source_step_nr)
                    ->first();

            $target_step = \App\Models\Workflow\WorkflowStep::where('workflow_def_id', $workflow->id)
                    ->where('step_nr', $target_step_nr)
                    ->first();

            if ($step && $target_step) {
                if ($arrow->is_yes == 1) {
                    $step->yes_step_nr = $target_step_nr;
                } else {
                    $step->no_step_nr = $target_step_nr;
                }

                $step->save();
            }
        }
    }

    public function getWFForm(Request $request)
    {
        $this->validate($request, [
            'item_id' => 'required|integer|exists:dx_workflows_def,id'
        ]);

        //\Log::info(json_endoce(Request::all()));

        $workflow_id = $request->input('item_id', 0);
        $list_id = $request->input('list_id', 0);

        $list = \App\Models\Lists::find($list_id);
        if ($list) {
            $list_title = $list->list_title;
            $wf_register_id = $list->id;
        } else {
            $list_title = '';
            $wf_register_id = 0;
        }

        if ($workflow_id > 0) {
            $workflow = \App\Models\Workflow\Workflow::find($workflow_id);
        }

        $grid_htm_id = $request->input('grid_htm_id', '');
        $frm_uniq_id = Uuid::generate(4);
        $is_disabled = false; //read-only rights by default

        $form_htm = view('workflow.visual_ui.wf_form', [
            'frm_uniq_id' => $frm_uniq_id,
            'form_title' => trans('task_form.form_title'),
            'is_disabled' => $is_disabled,
            'grid_htm_id' => $grid_htm_id,
            'item_id' => $workflow_id,
            'wf_register_id' => $wf_register_id,
            'wf_register_name' => $list_title,
            'workflow' => $workflow,
            'xml_data' => $this->prepareXML($workflow_id)
                ])->render();

        return response()->json(['success' => 1, 'html' => $form_htm]);
    }
}