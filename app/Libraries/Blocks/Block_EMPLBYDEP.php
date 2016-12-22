<?php
/**
 * Author:  Eugene Ostapenko <evo@olympsoft.com>
 * License: MIT
 * Created: 15.12.16, 16:19
 */

namespace App\Libraries\Blocks;

use App\Models\Department;
use App\Models\Source;
use App\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

/**
 * Class Block_EMPLBYDEP
 *
 * Widget that displays employees count by department.
 *
 * @package App\Libraries\Blocks
 */
class Block_EMPLBYDEP extends Block
{
	protected $totalCount;
	protected $sources;
	protected $departments;
	protected $counts;
	
	/**
	 * Render widget.
	 * @return string
	 */
	function getHtml()
	{
		$result = view('blocks.widget_emplbydep', [
			'sources' => $this->getSources(),
			'totalCount' => $this->getTotalCount(),
			'counts' => $this->getCounts()
		])->render();
		
		return $result;
	}
	
	/**
	 * Get total number of employees.
	 * @return mixed
	 */
	public function getTotalCount()
	{
		if($this->totalCount)
		{
			return $this->totalCount;
		}
		
		$this->totalCount = User::whereNotIn('id', Config::get('dx.empl_ignore_ids', [1]))->count();
		
		return $this->totalCount;
	}
	
	/**
	 * Get a list of sources.
	 * @return mixed
	 */
	public function getSources()
	{
		if($this->sources)
		{
			return $this->sources;
		}
		
		$sources = Source::orderBy('title')->get();
		
		foreach($sources as $source)
		{
			$this->sources[$source->id] = $source;
		}
		
		return $this->sources;
	}
	
	/**
	 * Get a list of departments.
	 * @return mixed
	 */
	public function getDepartments()
	{
		if($this->departments)
		{
			return $this->departments;
		}
		
		$departments = Department::orderBy('title')->get();
		
		foreach($departments as $department)
		{
			$this->departments[$department->id] = $department;
		}
		
		return $this->departments;
	}
	
	/**
	 * Get count of employees in each source.
	 * @return mixed
	 */
	public function getCounts()
	{
		if($this->counts)
		{
			return $this->counts;
		}
		
		$counts = DB::table('dx_users')
			->select(DB::raw('department_id, COUNT(*) AS count'))
			->whereNotIn('id', Config::get('dx.empl_ignore_ids', [1]))
			->groupBy('department_id')
			->get();
		
		$unassignedCount = 0;
		
		foreach($counts as $item)
		{
			if(!$item->department_id || !isset($this->getDepartments()[$item->department_id]))
			{
				$unassignedCount = $item->count;
				continue;
			}
			
			$source = $this->getDepartments()[$item->department_id]->source_id;
			
			if(!isset($this->counts[$source]))
			{
				$this->counts[$source]['count'] = $item->count;
			}
			
			else
			{
				$this->counts[$source]['count'] += $item->count;
			}
			
			$this->counts[$source]['percent'] = ($this->counts[$source]['count'] / $this->getTotalCount()) * 100;
		}
		
		if($unassignedCount)
		{
			$this->counts['unassigned'] = [
				'count' => $unassignedCount,
				'percent' => ($unassignedCount / $this->getTotalCount()) * 100
			];
		}
		
		return $this->counts;
	}
	
	function getJS()
	{
		// TODO: Implement getJS() method.
	}
	
	function getCSS()
	{
		return <<<END
			<style>
				.widget-emplbydep .progress {
					position: relative;
					background-color: #ddd;
				}
				.widget-emplbydep .progress, .widget-emplbydep .progress-bar {
					height: 20px;
				}
				.widget-emplbydep .progress-bar span, .widget-emplbydep .progress-bar a {
					color: #26344b;
					display: block;
					position: absolute;
					text-align: left;
					margin-left: 20px;
				}
			</style>
END;
	}
	
	function getJSONData()
	{
		// TODO: Implement getJSONData() method.
	}
	
	protected function parseParams()
	{
		// TODO: Implement parseParams() method.
	}
}