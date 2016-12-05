<?php

namespace App\Http\Controllers;

use App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades;

class OrgChartController extends Controller
{
	protected $rootId;
	protected $displayLevels;
	protected $employees;
	protected $index;
	protected $tree;
	protected $treeIndex;
	protected $levels = [];
	
	public function __construct()
	{
		$this->employees = $this->getEmployees();
		$this->index = $this->getIndex();
		
		$id = Facades\Route::current()->getParameter('id', 0);
		$this->rootId = $id ? $id : $this->index[0][0];
		$this->displayLevels = Facades\Request::input('displayLevels', 2);
		
		$this->tree = $this->getTree($this->rootId);
	}
	
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
	
	public function ajaxGetParent($id)
	{
		$parent = '';
		
		foreach($this->index as $key => $children)
		{
			if(in_array($id, $children))
			{
				$parent = $key;
				break;
			}
		}
		
		$employee = $this->employees[$parent];
		
		$hasParent = ($parent == $this->index[0][0]) ? '0' : '1';
		$hasChildren = empty($this->index[$parent]) ? '0' : '1';
		$hasSiblings = '0'; //count($node) > 1 ? '1' : '0';
		
		$tmp = [
			'id' => $employee->id,
			'name' => $employee->display_name,
			'title' => $employee->position_title ?: '',
			'avatar' => $employee->getAvatar(),
			'subordinates' => count($this->index[$parent]),
			'href' => route('profile', $employee->id),
			'relationship' => $hasParent . $hasSiblings . $hasChildren
		];
		
		return response($tmp);
	}
	
	/**
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
	 * $index = [
	 *   0    => [1263],
	 *   1263 => [1264, 1265],
	 *   1264 => [1267, 1268],
	 *   1267 => [1273],
	 *   1265 => [1269, 1270]
	 * ];
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
	 *    $arr = [
	 *        1263 => [
	 *            1264 => [
	 *                1267 => [
	 *                    1273 => []
	 *                ],
	 *                1268 => []
	 *            ],
	 *            1265 => [
	 *                1269 => [],
	 *                1270 => []
	 *            ]
	 *        ]
	 *    ];
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
	 * @param null $node
	 * @return array
	 */
	public function getOrgchartDatasource($node = null)
	{
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
				'relationship' => $hasParent . $hasSiblings . $hasChildren
			];
			
			if(!empty($subnode))
			{
				$tmp['children'] = $this->getOrgchartDatasource($subnode);
			}
			
			$result[] = $tmp;
		}
		
		return $result;
	}
}
