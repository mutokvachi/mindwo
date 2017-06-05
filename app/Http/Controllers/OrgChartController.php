<?php

namespace App\Http\Controllers;

use App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades;
use Illuminate\Support\Facades\DB;

/**
 * Class OrgChartController
 *
 * Controller for organization chart
 *
 * @package App\Http\Controllers
 */
class OrgChartController extends Controller
{
	/**
	 * Id of employee from which to start chart
	 * @var int
	 */
	protected $rootId;
	/**
	 * Number of chart levels to display from request
	 * @var int
	 */
	protected $displayLevels;
	/**
	 * Array of employees' models
	 * @var array
	 */
	protected $employees;
	/**
	 * Manager -> subordinates index
	 * @var array
	 */
	protected $index;
	/**
	 * Organizational hierarchy
	 * @var array
	 */
	protected $tree;
	/**
	 * Auxiliary hierarchy index
	 * @var array
	 */
	protected $treeIndex;
	/**
	 * Subordinary -> manager index
	 * @var array
	 */
	protected $parentsIndex;
	/**
	 * Array containing number of hierarchical levels under each employee
	 * @var array
	 */
	protected $levels = [];
	
	/**
	 * OrgChartController constructor.
	 */
	public function __construct()
	{
		$this->employees = $this->getEmployees();
		$this->index = $this->getIndex();
		$this->parentsIndex = $this->getParentsIndex();
		
		$id = Facades\Route::current()->getParameter('id', 0);
		$this->rootId = $id ? $id : Config::get('dx.orgchart.default_root_employee_id', 0);
		$this->displayLevels = (integer) Facades\Request::input('displayLevels', config('dx.orgchart.default_levels'));
		
		if($this->displayLevels < 3)
		{
			$this->displayLevels = 3;
		}
		
		$this->tree = $this->getTree($this->rootId);
	}
	
	/**
	 * Render organization chart.
	 *
	 * @param number|null $id Optional ID of an employee from which to start chart.
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function show($id = null)
	{
		$result = view('organization.chart', [
			'datasource' => $this->getOrgchartDatasource()[0],
			'rootId' => $this->rootId,
			'startLevels' => isset($this->levels[$this->rootId]) ? $this->levels[$this->rootId] : 1,
			'displayLevels' => $this->displayLevels,
			'employees' => $this->employees,
			'levels' => $this->levels,
		]);
		
		return $result;
	}
	
	/**
	 * Get all employees except admin users.
	 *
	 * @return array
	 */
	public function getEmployees()
	{
		$users =
			// raw expression is needed because of Eloquent is putting an ID from joined table into the model
			App\User::select(DB::raw('*, dx_users.id AS id'))
				->leftJoin('dx_users_positions AS p', 'dx_users.position_id', '=', 'p.id')
                                ->leftJoin('dx_users_jobtypes as jt', 'dx_users.job_type_id', '=', 'jt.id')
				// here we place employees without position to the end of the list
				->orderBy(DB::raw('dx_users.position_id IS NOT NULL'), 'DESC')
				// sorting by index
				->orderBy('p.order_index')
				// then by display name
				->orderBy('dx_users.display_name')
				// skip system users
				->whereNotIn('dx_users.id', config('dx.empl_ignore_ids', [1]))
                                ->whereNull('dx_users.termination_date')
                                ->where(function($query) {
                                    $query->whereNull('dx_users.job_type_id')
                                          ->orWhere('jt.is_hide_orgchart', '=', 0);
                                })
				->get();
		
		$employees = [];
		
		foreach($users as $user)
		{
			$employees[$user->id] = $user;
		}
		
		return $employees;
	}
	
	/**
	 * Build index of managers and their subordinates.
	 *
	 * Example of index structure:
	 *
	 * ```php
	 * $index = [
	 *   0    => [1263],
	 *   1263 => [1264, 1265],
	 *   1264 => [1267, 1268],
	 *   1267 => [1273],
	 *   1265 => [1269, 1270]
	 * ];
	 * ```
	 *
	 * @return array
	 */
	public function getIndex()
	{
		$index = [];
		
		foreach($this->employees as $employee)
		{
			$index[(int) $employee->manager_id][] = $employee->id;
		}
		
		return $index;
	}
	
	/**
	 * Build index of employees and their direct managers.
	 *
	 * Example of index structure:
	 *
	 * ```php
	 * $index = [
	 *   1263 => 0,
	 *   1264 => 1263,
	 *   1265 => 1263,
	 *   1267 => 1264,
	 *   1268 => 1264,
	 *   1273 => 1267,
	 *   1269 => 1265,
	 *   1270 => 1265
	 * ];
	 * ```
	 *
	 * @return array
	 */
	public function getParentsIndex()
	{
		$index = [];
		
		foreach($this->index as $parent => $children)
		{
			foreach($children as $child)
			{
				$index[$child] = $parent;
			}
		}
		
		return $index;
	}
	
	/**
	 * Recursively traverse index of managers and subordinates and build multi-dimensional array describing
	 * organizational hierarchy.
	 *
	 * Example of hierarchy:
	 *
	 * ```php
	 * $arr = [
	 *     1263 => [
	 *         1264 => [
	 *             1267 => [
	 *                 1273 => []
	 *             ],
	 *             1268 => []
	 *         ],
	 *         1265 => [
	 *             1269 => [],
	 *             1270 => []
	 *         ]
	 *     ]
	 * ];
	 * ```
	 *
	 * @return array
	 */
	public function getTree($rootId)
	{
		$buildTree = function ($manager_id, &$subtree) use (&$buildTree)
		{
			if(isset($this->index[$manager_id]))
			{
				$deepest = true;
				$sublevels = 2;
				
				foreach($this->index[$manager_id] as $id)
				{
					$subtree[$id] = [];
					$buildTree($id, $subtree[$id]);
					
					if(isset($this->index[$id]))
					{
						$deepest = false;
					}
					
					if(isset($this->levels[$id]) && $this->levels[$id] > $sublevels)
					{
						$sublevels = $this->levels[$id];
					}
				}
				
				if($deepest)
				{
					$this->levels[$manager_id] = 2;
				}
				else
				{
					$this->levels[$manager_id] = $sublevels + 1;
				}
				
				$this->treeIndex[$manager_id] = $subtree;
			}
		};
		
		$tree = [];
		
		$buildTree(0, $tree);
		
		return [$rootId => $tree];
	}
	
	/**
	 * Generate multi-dimensional array containing organizational data required by OrgChart JavaScript plugin.
	 *
	 * @param array|null $node
	 * @return array
	 */
	public function getOrgchartDatasource($node = null)
	{
		$top = $node ? false : true;
		
		// first pass - determine top element
		if(!$node)
		{
			// employee id isn't specified - draw whole tree from the root
			if($this->rootId == 0)
			{
				// there is only one top-level employee in hierarchy (without manager)
				if(count($this->treeIndex[0]) == 1)
				{
					$node = $this->treeIndex[0];
				}
				
				// more than one employees without manager - need to generate fake 'company' root element
				else
				{
					$node = [0 => $this->treeIndex[0]];
				}
			}
			
			// employee with requested id doesn't exist - emit 404
			elseif(!isset($this->employees[$this->rootId]))
			{
				abort(404);
			}
			
			// employee doesn't have any subordinates
			elseif(!isset($this->treeIndex[$this->rootId]))
			{
				$node = [$this->rootId => []];
			}
			
			// employee exists - draw corresponding subtree
			else
			{
				$node = [$this->rootId => $this->treeIndex[$this->rootId]];
			}
		}
		
		$result = [];
		
		foreach($node as $id => $subnode)
		{
			// id is not null - the root element is person
			if($id)
			{
				$hasSiblings = count($node) > 1 ? '1' : '0';
				$hasChildren = empty($subnode) ? '0' : '1';
				$hasParent = ($id == $this->index[0][0]) ? '0' : '1';
				
				$employee = $this->employees[$id];
				
				$tmp = [
					'id' => $employee->id,
					'name' => $employee->display_name,
					'title' => $employee->position_title ?: '',
					//'avatar' => $employee->getAvatar(),
					'subordinates' => count($subnode),
					'href' => route('profile', $employee->id),
					'relationship' => $hasParent . $hasSiblings . $hasChildren,
					'hasParent' => $hasParent == '1',
					'top' => $top,
				];
				
				// add url of a parent node
				if($top)
				{
					if($parent = $this->parentsIndex[$id])
					{
						$tmp['parentUrl'] = route('organization_chart', $parent);
					}
					else
					{
						$tmp['parentUrl'] = route('organization_chart');
					}
				}
			}
			
			// id is null - there are more than one top managers, generate a fake "Company" top-level element
			else
			{
				$tmp = [
					'id' => 0,
					'name' => config('dx.company.short_title'),
					'title' => trans('organization.company'),
					//'avatar' => url(config('dx.company.logo')),
					'subordinates' => count($subnode),
					'href' => route('organization_departments'),
					'relationship' => '001',
					'hasParent' => false,
					'top' => true,
				];
			}
			
			// recurse deeper to the next level of hierarchy
			if(!empty($subnode))
			{
				$tmp['children'] = $this->getOrgchartDatasource($subnode);
			}
			// even if employee doesn't have any subordinates, we must provide an empty array of children,
			// for OrgChart plugin to work correctly
			else
			{
				$tmp['children'] = [];
			}
			
			$result[] = $tmp;
		}
		
		return $result;
	}
}
