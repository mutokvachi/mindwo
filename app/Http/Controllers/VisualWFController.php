<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Workflow\WorkflowStep;
use Illuminate\Http\Request;
use Webpatser\Uuid\Uuid;
use Sunra\PhpSimple\HtmlDomParser;

/**
 * Visual workflows controller
 */
class VisualWFController extends Controller
{

    /**
     * List of cell's Ids
     * @var array
     */
    private $xml_cell_list = [];

    /**
     * Current workflow's Id
     * @var int
     */
    private $workflow_id = 0;

    /**
     * Array of error found in validation process
     * @var array
     */
    private $error_stack = [];

    /**
     * Current step counter when ordering steps on save
     *
     * @var integer
     */
    private $step_counter = 0;

    /**
     * ID's of all saved steps on saving operation. All other which are not saved will be deleted.
     *
     * @var array
     */
    private $saved_steps = [];

    /**
     * Here we load all the workflow steps from DB before processing.
     */
	private $steps = [];
	/**
	 * Here we store once evaluated [ id => step_nr ] pairs to avoid multiple evaluation of step number.
	 */
	private $step_numbers = [];
    
    /**
     * Test method to draw graph
     * @return type
     */
    public function test()
    {
        $workflow_id = 1;

        $workflow = \App\Models\Workflow\Workflow::find($workflow_id);

        $wf_register_id = $workflow->list_id; //60

        /*
          'is_disabled' => $is_disabled,
          'grid_htm_id' => $grid_htm_id,
          'item_id' => $workflow_id,
          'wf_register_id' => $wf_register_id,
          'wf_register_name' => $list_title,
          'wf_data' => $workflow,
          'xml_data' => $this->prepareXML($workflow_id)
         */

        $xml_data = $this->prepareXML($workflow_id);

        $max_step = $this->getLastStep($workflow);

        if ($max_step) {
            $max_step_nr = $max_step->step_nr;
        } else {
            $max_step_nr = 0;
        }

        return  view('pages.wf_test');
        
        return view('pages.wf_test', ['json_data' => '',
                    'portal_name' => 'Mindwo',
                    'form_title' => 'Workflow',
                    'item_id' => $workflow_id,
                    'wf_register_id' => $wf_register_id,
                    'is_disabled' => false,
                    'wf_register_id' => $wf_register_id,
                    'wf_register_name' => 'Laba liste',
                    'workflow' => $workflow,
                    'xml_data' => $xml_data,
                    'max_step_nr' => $max_step_nr])->render();
    }

    /**
     * Gets first step of workflow
     * @param \App\Models\Workflow\Workflow $workflow Workflow object
     * @return \App\Models\Workflow\WorkflowStep First workflow's step
     */
    public function getFirstStep($workflow)
    {
        if ($workflow) {
            return $workflow->workflowSteps()->orderBy('step_nr', 'ASC')->first();
        } else {
            return null;
        }
    }

    /**
     * Gets last step of workflow
     * @param \App\Models\Workflow\Workflow $workflow Workflow object
     * @return \App\Models\Workflow\WorkflowStep First workflow's step
     */
    public function getLastStep($workflow)
    {
        if ($workflow) {
            return $workflow->workflowSteps()->orderBy('step_nr', 'DESC')->first();
        } else {
            return null;
        }
    }

    /**
     * Prepares Xml from database
     * @param string $xml_str Xml in string format
     * @return string Prepared Xml string
     */
    private function prepareXmlFromDb($xml_str)
    {
        // Machination to overcome problem when node contains texts with "", then xml is echoed in html like this -> value="This is sample "text""
        $xml = simplexml_load_string($xml_str);

        $cells = $xml->xpath('/mxGraphModel/root/mxCell');

        foreach ($cells as $cell) {
            $cell['value'] = htmlspecialchars($cell['value'], ENT_QUOTES, 'UTF-8');
        }

        $dom = dom_import_simplexml($xml);
        return $dom->ownerDocument->saveXML($dom->ownerDocument->documentElement);
    }

    /**
     * Delete steps which are not connected
     *
     * @param App\Models\Workflow\Workflow $workflow Current workflow
     * @param App\Models\Workflow\WorkflowStep $first_step First step of current workflow
     * @return void
     */
    private function deleteNotConnected($workflow, $first_step)
    {
        $workflow_steps = \DB::Select('select d.id
            from dx_workflows d
            left join dx_workflows dc on d.workflow_def_id = dc.workflow_def_id
                    AND (d.step_nr = dc.yes_step_nr OR d.step_nr = dc.no_step_nr)
            where d.workflow_def_id = ?
            group by d.id
            having count(dc.id) <= 0', [$workflow->id]);

        $first_step_id = ($first_step ? $first_step->id : 0);

        $workflow_steps_ids = [];
        foreach ($workflow_steps as $step) {
            if ($step->id != $first_step_id) {
                $workflow_steps_ids[] = $step->id;
            }
        }

        \App\Models\Workflow\WorkflowStep::destroy($workflow_steps_ids);
    }


    /**
     * Deletes workflow step
     * @param Request $request Data request
     * @return string Json response
     */
    public function deleteStep(Request $request)
    {
        $step_id = $request->input('step_id');

        \App\Models\Workflow\WorkflowStep::destroy($step_id);

        return response()->json(['success' => 1]);
    }

    /**
     * Prepares Xml for specified workflow
     * @param int $workflowId Workflow's Id
     * @param boolean $getFromDb Parameter if get Xml from database if it is available
     * @return string Prepared Xml
     */
    public function prepareXML($workflowId, $getFromDb = true)
    {
        $workflow = \App\Models\Workflow\Workflow::find($workflowId);

        if (!$workflow) {
            return '';
        }

        $workflow_step = $this->getFirstStep($workflow);

        $this->deleteNotConnected($workflow, $workflow_step);

        if ($getFromDb && $workflow->visual_xml) {
            // Varbūt pirms notiek saglabāšana varam iziet visiem elmentiem cauti un izpildīt encode, lai salabo ielādētās pēdiņas.
            // return $workflow->visual_xml;
            return $this->prepareXmlFromDb($workflow->visual_xml);
        }

        $this->workflow_id = $workflowId;

        if (!$workflow_step) {
            return '';
        }

        $xml = new \SimpleXMLElement('<mxGraphModel />');

        $root = $xml->addChild('root');

        $mxCell = $root->addChild('mxCell');
        $mxCell->addAttribute('id', '0');

        $mxCell = $root->addChild('mxCell');
        $mxCell->addAttribute('id', '1');
        $mxCell->addAttribute('parent', '0');

        $this->createMxCell($root, false, -1, 2, $workflow_step->step_nr, 0, 'ENDPOINT', 30, 30);

        //$this->createMxCell($root, $workflow_step->step_nr, $workflow_step->yes_step_nr, $workflow_step->no_step_nr, ($workflow_step->task_type_id == 5 ? 'rhombus' : 'rounded'));

        // Removes 'xml version="1.0"' at the beginning of the xml
        $dom = dom_import_simplexml($xml);
        return $dom->ownerDocument->saveXML($dom->ownerDocument->documentElement);
    }

    /**
     *
     * @param SimpleXMLElement $root Xml object
     * @param string $value Value of the cell
     * @param string $step_id Step's ID
     * @param string $step_nr Step's number
     * @param string $yes_step_nr Yes step's number
     * @param string $no_step_nr No step's number
     * @param string $type_code Steps type
     * @param int $x Position X
     * @param int $y Position Y
     * @return void
     */
    private function createMxCell(&$root, $value, $step_id, $step_nr, $yes_step_nr, $no_step_nr, $type_code, $x, $y)
    {
        // Exit if cell already exist
        if (in_array($step_nr, $this->xml_cell_list)) {
            return;
        }
        
        $this->xml_cell_list[] = $step_nr;

        $mxCell = $root->addChild('mxCell');

        $has_arrow_labels = 0;

        if (in_array($type_code, [ 'APPR', 'EXEC', 'SUPP', 'CRIT', 'CRITM' ])) {
            $has_arrow_labels = 1;
            $arrow_count = 2;
            $shape = 'rhombus';
        } elseif ($type_code == 'ENDPOINT') {
            $arrow_count = 1;
            $shape = 'ellipse';
        } elseif (in_array($type_code, [ 'SET', 'INFO'])) {
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

        $mxCell->addAttribute('style', 'fillColor=#E1E5EC;strokeColor=#4B77BE;fontColor=black;editable=0;html=1;whiteSpace=wrap;shape=' . $shape);

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
        if ($yes_step_nr && $yes_step_nr > 0) {
            $this->createNextCell($root, $yes_step_nr, $x, $y + $height + 30, $shape, $step_nr, true);
        } elseif ($type_code != 'ENDPOINT') {
            $this->createMxCell($root, false, -1, ($step_nr + 1), 0, 0, 'ENDPOINT', $x, $y + $height + 30);

            $arrow_text = ($type_code == 'CRIT' || $type_code == 'CRITM') ? trans('workflow.yes') : '';
            $this->createArrow($root, $step_nr, ($step_nr + 1), $arrow_text, true);
        }

        if ($no_step_nr && $no_step_nr > 0) {
            $this->createNextCell($root, $no_step_nr, $x + $width + 40, $y + $height + 30, $shape, $step_nr, false);
        } elseif (($type_code == 'CRIT' || $type_code == 'CRITM') && $type_code != 'ENDPOINT') {
            $this->createMxCell($root, false, -1, ($step_nr + 1), 0, 0, 'ENDPOINT', $x + $width + 40, $y + $height + 30);

            $arrow_text = ($type_code == 'CRIT' || $type_code == 'CRITM') ? trans('workflow.no') : '';
            $this->createArrow($root, $step_nr, ($step_nr + 1), $arrow_text, false);
        }
    }

    /**
     *
     * @param SimpleXMLElement $root Xml object
     * @param string $next_step_nr Next steps number
     * @param int $x Position X
     * @param int $y Position Y
     * @param string $parent_shape Type of parent shape
     * @param string $parent_step_nr Parent's step number
     * @param bool $is_yes_arrrow Parameter whichc shows if arrow is with value yes or no
     */
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

    /**
     * Creates arrow graph object
     * @param SimpleXMLElement $root Xml object
     * @param string $parent Parents Id
     * @param string $child Childs Id
     * @param string $value Text of the arrow
     * @param bool $is_yes_arrrow Parameter whichc shows if arrow is with value yes or no
     */
    private function createArrow(&$root, $parent, $child, $value, $is_yes_arrrow)
    {
        $mxCell = $root->addChild('mxCell');

        $mxCell->addAttribute('edge', '1');
        $mxCell->addAttribute('parent', '1');
        $mxCell->addAttribute('source', 's' . $parent);
        $mxCell->addAttribute('target', 's' . $child);
        // labelBackgroundColor=white
        $mxCell->addAttribute('style', 'editable=0;fillColor=#E1E5EC;strokeColor=#4B77BE;fontColor=black;labelPosition=right;align=left;');
        $mxCell->addAttribute('is_yes', $is_yes_arrrow ? '1' : '0');

        if ($value) {
            $mxCell->addAttribute('value', htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
        }

        $mxGeometry = $mxCell->addChild('mxGeometry');
        $mxGeometry->addAttribute('relative', '1');
        $mxGeometry->addAttribute('as', 'geometry');
    }

    /**
     * Saves workflow's data
     * @param Request $request Data request
     * @return string Json response
     */
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
            $workflow->created_user_id = \Auth::user()->id;
            $workflow->created_time = new \DateTime();
        }

        $xlm_data = $request->input('xml_data');

        $has_wf_details = $request->input('has_wf_details') == 1;

        $wf_title = $request->input('title');

        if ($has_wf_details == 1) {
            if (!$wf_title || strlen(trim($wf_title)) <= 0) {
                return response()->json(['success' => 0]);
            }

            $workflow->list_id = $request->input('list_id');
            $workflow->title = trim($wf_title);
            $workflow->description = $request->input('description');
            $workflow->is_custom_approve = $request->input('is_custom_approve');

            $workflow->modified_user_id = \Auth::user()->id;
            $workflow->modified_time = new \DateTime();

            $date_format = config('dx.txt_date_format');
            $valid_to = $request->input('valid_to');
            if ($valid_to && strlen(trim($valid_to)) > 0) {
                $workflow->valid_to = date_create_from_format($date_format, $request->input('valid_to'));
            }

            $valid_from = $request->input('valid_from');
            if ($valid_from && strlen(trim($valid_from)) > 0) {
                $workflow->valid_from = date_create_from_format($date_format, $request->input('valid_from'));
            }
        }

        if ($xlm_data && strlen(trim($xlm_data)) > 0) {
            $workflow->visual_xml = $xlm_data;
            $xml = HtmlDomParser::str_get_html($workflow->visual_xml);

            $first_point_id = $this->validateEndpoints($xml);

            $this->validateStepsConnections($workflow, $xml);

            if (count($this->error_stack) > 0) {
                return response()->json(['success' => 0, 'errors' => $this->error_stack]);
            }

            $this->saveRelations($workflow, $xml, $first_point_id);
        }

        $workflow->save();
	
		return response()->json([
			'success' => 1,
			'html' => $workflow->id,
			'numbers' => $this->step_numbers
		]);
	}

    /**
     * Validate if endpoints are correct in workflow
     * @param HtmlDomParser $xml Xml object
     * @return integer Workflows starting point
     */
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

        return $start_points_ids[0];
    }

    /**
     * Validate workflows step's connections
     * @param \App\Models\Workflow\Workflow $workflow Workflow model
     * @param HtmlDomParser $xml Xml object
     */
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

    /**
     * Saves relations between steps
     * @param \App\Models\Workflow\Workflow $workflow Workflow model
     * @param HtmlDomParser $xml Xml object
     * @param integer $first_point_id Id of first endpoint
     */
    private function saveRelations($workflow, $xml, $first_point_id)
    {
        // Finds first step
        $first_step_element_id = $xml->find('mxCell[source='. $first_point_id . ']', 0)->target;

        $this->step_counter = 10;
        $this->saved_steps = [];
        $first_step_element = $xml->find('mxCell[id='. $first_step_element_id . ']', 0);
        
        $this->loadSteps($xml);
        
        \DB::transaction(function () use ($workflow, $xml, $first_step_element, $first_step_element_id) {
				//$this->orderStep($xml, $first_step_element, $first_step_element_id);
			
        		$this->orderSteps($xml, $first_step_element_id);
				
        		foreach($this->steps as $step)
				{
					$step->save();
				}

                $this->deleteNotSavedSteps($workflow->id);
        });
    }
	
	/**
	 * Load all steps before ordering - separate database operations from calculations.
	 *
	 * @param $xml
	 */
    private function loadSteps($xml)
	{
		$cells = $xml->find('mxCell[workflow_step_id]');
		
		foreach($cells as $cell)
		{
			$id = $cell->workflow_step_id;
			if($id < 1)
			{
				continue;
			}
			$this->steps[$id] = WorkflowStep::find($id);
		}
	}
	
	/**
	 * Calculate step numbers taking into account already calculated and cached values.
	 *
	 * @param $xml
	 * @param $cell_id
	 */
	private function orderSteps($xml, $cell_id)
	{
		$element = $xml->find('mxCell[id='. $cell_id . ']', 0);
		
		$id = $element->workflow_step_id;
		$step = $this->steps[$id];
		
		$this->saved_steps[] = $id;
		
		if(isset($this->step_numbers[$id]))
		{
			return;
		}
		
		$step->step_nr = $this->step_numbers[$id] = $this->step_counter;
		$this->step_counter += 10;
		
		$edges = $xml->find('mxCell[source=' . $cell_id . ']');
		
		foreach($edges as $edge)
		{
			$next = $xml->find('mxCell[id='. $edge->target . ']', 0);
			
			if($next->type_code == 'ENDPOINT')
			{
				if($edge->is_yes == 1)
				{
					$step->yes_step_nr = 0;
				}
				else
				{
					$step->no_step_nr = 0;
				}
				
				continue;
			}
			
			$next_cell_id = $next->id;
			$next_id = $next->workflow_step_id;
			
			if(!isset($this->step_numbers[$next_id]))
			{
				$this->orderSteps($xml, $next_cell_id);
			}
			
			if($edge->is_yes == 1)
			{
				$step->yes_step_nr = $this->step_numbers[$next_id];
			}
			else
			{
				$step->no_step_nr = $this->step_numbers[$next_id];
			}
		}
	}

    /**
     * Deletes all steps that where not connected to workflow
     *
     * @param integer $workflow_id Current workflow id
     * @return void
     */
    private function deleteNotSavedSteps($workflow_id)
    {
        \App\Models\Workflow\WorkflowStep::where('workflow_def_id', $workflow_id)
            ->whereNotIn('id', $this->saved_steps)
            ->delete();
    }

    /**
     * Recursive order steps
     *
     * @param HtmlDomParser $xml Xml object
     * @param object $step_element Current step element which will be reordered
     * @param integer $step_element_id Current steps element id which will be ordered
     * @return void
     */
    private function orderStep($xml, $step_element, $step_element_id)
    {
        // Gets current step
        $step_element  = $xml->find('mxCell[id='. $step_element_id . ']', 0);

        $this->saved_steps[] = $step_element->workflow_step_id;

        // Update current step number
        $step = \App\Models\Workflow\WorkflowStep::find($step_element->workflow_step_id);
        $step->step_nr = $this->step_counter;

        // Finds all edges
        $step_edges = $xml->find('mxCell[source=' . $step_element_id . ']');

        // Iterate all edges
        foreach ($step_edges as $step_edge) {
            // Finds element by edge
            $next_step_elem = $xml->find('mxCell[id='. $step_edge->target . ']', 0);

            // If element is endpoint then we quit
            if ($next_step_elem->type_code == 'ENDPOINT') {
                // Resets value because it can be saved in data base
                if ($step_edge->is_yes == 1) {
                    $step->yes_step_nr = 0;
                } else {
                    $step->no_step_nr = 0;
                }

                continue;
            }

            // Rise step counter
            $this->step_counter += 10;

            // Connect our current step with next step depending if it is yes or no step
            if ($step_edge->is_yes == 1) {
                $step->yes_step_nr = $this->step_counter;
            } else {
                $step->no_step_nr = $this->step_counter;
            }

            // Move to next step
            $this->orderStep($xml, $next_step_elem, $step_edge->target);
        }

        $step->save();
    }

    /**
     * Gets graph's Xml arranged automatically
     * @param int $workflow_id Workflow's identifier
     * @return string Workflows XML for graph
     */
    public function getXml($workflow_id)
    {
        $xml = $this->prepareXML($workflow_id, false);

        return response()->json(['success' => 1, 'html' => $xml]);
    }

    /**
     * Get forms HTML
     * @param Request $request Data request
     * @return string Json response containing HTML
     */
    public function getWFForm(Request $request)
    {
        $this->validate($request, [
            'item_id' => 'required|integer|exists:dx_workflows_def,id'
        ]);

        $workflow_id = $request->input('item_id', 0);

        if ($workflow_id > 0) {
            $workflow = \App\Models\Workflow\Workflow::find($workflow_id);
        } else {
            return 'Workflow not saved';
        }
        
        $list_id = $workflow->list_id;

        $list = \App\Models\System\Lists::find($list_id);
        if ($list) {
            $list_title = $list->list_title;
            $wf_register_id = $list->id;
        } else {
            $list_title = '';
            $wf_register_id = 0;
        }

        $max_step = $this->getLastStep($workflow);

        if ($max_step) {
            $max_step_nr = $max_step->step_nr;
        } else {
            $max_step_nr = 0;
        }

        $grid_htm_id = $request->input('grid_htm_id', '');
        $frm_uniq_id = Uuid::generate(4);
        $is_disabled = false; //read-only rights by default

        $form_htm = view('workflow.visual_ui.wf_component', [
            'frm_uniq_id' => $frm_uniq_id,
            'form_title' => trans('workflow.form_title'),
            'is_disabled' => $is_disabled,
            'grid_htm_id' => $grid_htm_id,
            'item_id' => $workflow_id,
            'wf_register_id' => $wf_register_id,
            'wf_register_name' => $list_title,
            'workflow' => $workflow,
            'xml_data' => $this->prepareXML($workflow_id),
            'max_step_nr' => $max_step_nr
                ])->render();

        return response()->json(['success' => 1, 'html' => $form_htm]);
    }
}
