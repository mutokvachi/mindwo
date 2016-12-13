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
class DepartmentsChartController extends Controller
{
	/**
	 * Number of chart levels to display from request
	 * @var int
	 */
	protected $displayLevels;
	/**
	 * Array of departments' models
	 * @var array
	 */
	protected $departments;
	/**
	 * Departments -> subordinate departments index
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
	protected $levels = [];
	
	protected $counts;
	
	/**
	 * OrgChartController constructor.
	 */
	public function __construct()
	{
		$this->departments = $this->getDepartments();
		$this->index = $this->getIndex();
		$this->displayLevels = Facades\Request::input('displayLevels', 2);
		$this->tree = $this->getTree(0);
		
		$counts = DB::table('dx_users')
			->select(DB::raw('department_id, COUNT(*) as count'))
			->groupBy('department_id')
			->get();
		
		foreach($counts as $item)
		{
			$this->counts[$item->department_id] = $item->count;
		}
	}
	
	/**
	 * Render organization chart.
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function show()
	{
		$result = view('organization.departments', [
			'datasource' => $this->getOrgchartDatasource(),
			'displayLevels' => $this->displayLevels,
			'departments' => $this->departments,
			'levels' => $this->levels,
		]);
		
		return $result;
	}
	
	/**
	 * Get all departments.
	 *
	 * @return array
	 */
	public function getDepartments()
	{
		$items = App\Models\Department::orderBy('title')->get();
		
		$result = [];
		
		foreach($items as $item)
		{
			$result[$item->id] = $item;
		}
		
		return $result;
	}
	
	/**
	 * Build index of departments and their subordinate departments.
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
		
		foreach($this->departments as $department)
		{
			$index[(int) $department->parent_id][] = $department->id;
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
		$buildTree = function ($parent_id, &$subtree) use (&$buildTree)
		{
			if(isset($this->index[$parent_id]))
			{
				$deepest = true;
				$sublevels = 2;
				
				foreach($this->index[$parent_id] as $id)
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
					$this->levels[$parent_id] = 2;
				}
				else
				{
					$this->levels[$parent_id] = $sublevels + 1;
				}
				
				$this->treeIndex[$parent_id] = $subtree;
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
		if(!$node)
		{
			$node = $this->tree[0];
			
			$result0 = [
				'id' => 0,
				'name' => 'Company', //trans('organization.company'),
				'title' => '',
				'subordinates' => count($node),
				'source_id' => 2,
				'relationship' => '001',
			];
		}
		
		$result = [];
		
		foreach($node as $id => $subnode)
		{
			$hasParent = '1';
			$hasChildren = empty($subnode) ? '0' : '1';
			$hasSiblings = count($node) > 1 ? '1' : '0';
			
			$department = $this->departments[$id];
			
			$tmp = [
				'id' => $department->id,
				'name' => $department->title,
				'title' => '',
				'subordinates' => count($subnode),
				'source_id' => $department->source_id,
				'count' => $this->counts[$department->id],
				'relationship' => $hasParent . $hasSiblings . $hasChildren,
			];
			
			if(!empty($subnode))
			{
				$tmp['children'] = $this->getOrgchartDatasource($subnode);
			}
			
			$result[] = $tmp;
		}
		
		if(isset($result0))
		{
			$result0['children'] = $result;
			return $result0;
		}
		
		return $result;
	}
}
