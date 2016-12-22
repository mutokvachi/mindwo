<?php
/**
 * Author:  Eugene Ostapenko <evo@olympsoft.com>
 * License: MIT
 * Created: 15.12.16, 16:19
 */

namespace App\Libraries\Blocks;

use App\User;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

/**
 * Class Block_EMPLBYTEAM
 *
 * Widget that displays employees count by team.
 *
 * @package App\Libraries\Blocks
 */
class Block_EMPLBYTEAM extends Block
{
	protected $totalCount;
	protected $unassignedCount;
	protected $teams;
	protected $counts;
	
	/**
	 * Render widget.
	 * @return string
	 */
	function getHtml()
	{
		$result = view('blocks.widget_emplbyteam', [
			'totalCount' => $this->getTotalCount(),
			'teams' => $this->getTeams(),
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
	 * Get a list of all teams.
	 * @return mixed
	 */
	public function getTeams()
	{
		if($this->teams)
		{
			return $this->teams;
		}
		
		$teams = Team::orderBy('title')->get();
		
		foreach($teams as $team)
		{
			$this->teams[$team->id] = $team;
		}
		
		return $this->teams;
	}
	
	/**
	 * Get an array with count of employees in each team.
	 * @return mixed
	 */
	public function getCounts()
	{
		if($this->counts)
		{
			return $this->counts;
		}
		
		$counts = DB::table('dx_users')
			->select(DB::raw('team_id, COUNT(*) AS count'))
			->whereNotIn('id', Config::get('dx.empl_ignore_ids', [1]))
			->groupBy('team_id')
			->get();
		
		$unassignedCount = 0;
		
		foreach($counts as $item)
		{
			if(!$item->team_id || !isset($this->getTeams()[$item->team_id]))
			{
				$unassignedCount++;
				continue;
			}
			
			$this->counts[$item->team_id] = [
				'count' => $item->count,
				'percent' => ($item->count / $this->getTotalCount()) * 100
			];
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
				.widget-emplbyteam .progress {
					position: relative;
					background-color: #ddd;
				}
				.widget-emplbyteam .progress, .widget-emplbyteam .progress-bar {
					height: 20px;
				}
				.widget-emplbyteam .progress-bar span, .widget-emplbyteam .progress-bar a {
					color: white;
					display: block;
					position: absolute;
					text-align: left;
					margin-left: 20px;
					text-shadow: 0px 0px 2px #333;
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