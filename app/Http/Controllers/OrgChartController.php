<?php

namespace App\Http\Controllers;

use App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades;

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
		$this->rootId = $id ? $id : $this->index[0][0];
		$this->displayLevels = Facades\Request::input('displayLevels', 2);
		
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
		$users = App\User::whereNotIn('id', Config::get('dx.empl_ignore_ids', [1]))
			->orderBy('display_name')
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
	 * Recursively traverse index of managers and subordinates and build muilti-dimensional array describing
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
		
		$buildTree($this->index[0][0], $tree);
		
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
		
		if(!$node)
		{
			if(isset($this->treeIndex[$this->rootId]))
			{
				$node = [$this->rootId => $this->treeIndex[$this->rootId]];
			}
			
			else
			{
				$node = [$this->rootId => []];
			}
		}
		
		$result = [];
		
		foreach($node as $id => $subnode)
		{
			$hasParent = ($id == $this->index[0][0]) ? '0' : '1';
			$hasChildren = empty($subnode) ? '0' : '1';
			$hasSiblings = count($node) > 1 ? '1' : '0';
			
			$employee = $this->employees[$id];
			
			$tmp = [
				'id' => $employee->id,
				'name' => $employee->display_name,
				'title' => $employee->position_title ?: '',
				'avatar' => $employee->getAvatar(),
				'subordinates' => count($subnode),
				'href' => route('profile', $employee->id),
				'relationship' => $hasParent . $hasSiblings . $hasChildren,
				'hasParent' => $hasParent == '1',
				'top' => $top
			];
			
			if($top)
			{
				$tmp['parentUrl'] = route('organization_chart', $this->parentsIndex[$id]);
			}
			
			if(!empty($subnode))
			{
				$tmp['children'] = $this->getOrgchartDatasource($subnode);
			}
			
			$result[] = $tmp;
		}
		
		return $result;
	}
}
