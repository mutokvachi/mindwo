<?php

namespace App\Libraries\Blocks;

use App;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Config;

/**
 * Class Block_CONGRATULATE
 *
 * Widget that displays birthdays and work anniversaries.
 *
 * @package App\Libraries\Blocks
 */
class Block_CONGRATULATE extends Block
{
	protected $employees;
	
        /**
         * Indicates if system have profile UI functionality
         * @var type 
         */
        public $is_profile = false;
        
	/**
	 * Render widget and return its HTML.
	 *
	 * @return string
	 */
	public function getHtml()
	{
		$result = view('blocks.widget_congratulate', [
			'self' => $this,
			'employees' => $this->getEmployees()
		])->render();
		
		return $result;
	}
	
	/**
	 * Get a text string that describes kind of an event - birthday or work anniversary.
	 *
	 * @param $employee
	 * @return string
	 */
	public function getTypeOfEvent($employee)
	{
		if($employee->birth_date && !$employee->join_date)
		{
			return trans('congratulate.lbl_birthday');
		}
		
		if(!$employee->birth_date && $employee->join_date)
		{
			return $this->getAnniversTxt($employee->join_date);
		}
		
		$now = Carbon::now(Config::get('dx.time_zone'));
		
		$birth = Carbon::createFromFormat('Y-m-d', $employee->birth_date);
		
		if($birth->month == $now->month && $birth->day == $now->day)
		{
			return trans('congratulate.lbl_birthday');
		}
		
		else
		{
			return $this->getAnniversTxt($employee->join_date);
		}
	}
	
	/**
	 * Returns JavaScript that calculates appropriate height of a widget.
	 *
	 * @return string
	 */
	public function getJS()
	{
		return <<<END
			<script>
				$(document).ready(function(){
					var items = $('.widget-congratulate .mt-actions > .mt-action');
					var mult = (items.length < 3 ? items.length : 3);
					$('.widget-congratulate .mt-actions').slimScroll({
						height: (items.first().outerHeight() * mult) + 'px'
					});
				});
			</script>
END;
	}
	
	/**
	 * Returns widget's styles.
	 *
	 * @return string
	 */
	public function getCSS()
	{
		return <<<END
			<style>
				.widget-congratulate .mt-action-img img {
					width: 45px;
					height: 45px;
				}
			</style>
END;
	}
	
	public function getJSONData()
	{
		// TODO: Implement getJSONData() method.
	}
	
	/**
	 * Find employees that have birthday or work anniversary today.
	 *
	 * @return mixed
	 */
	protected function getEmployees()
	{
		if($this->employees)
		{
			return $this->employees;
		}
		
		$now = Carbon::now(Config::get('dx.time_zone'));
		$user = App\User::find(Auth::user()->id);
		
		$this->employees = App\User::where(function ($query) use ($now)
		{
			$query
				->whereMonth('birth_date', '=', $now->month)
				->whereDay('birth_date', '=', $now->day);
		})
			->orWhere(function ($query) use ($now)
			{
				$query
					->whereMonth('join_date', '=', $now->month)
					->whereDay('join_date', '=', $now->day);
			})
			->get()
			// check permissions
			->filter(function ($employee) use ($user)
			{
				// logged user is admin
				if($user->id == 1)
				{
					return true;
				}
				
				// employee doesn't have any access rights specified
				if(!count($employee->access))
				{
					return true;
				}
				
				// check if logged in user has the same access role
				foreach($employee->access as $role)
				{
					$tmp = $user->access->filter(function ($item) use ($role)
					{
						return $item->id == $role->id;
					});
					
					if(count($tmp))
					{
						return true;
					}
				}
				
				return false;
			});
		
		return $this->employees;
	}
	
	protected function parseParams()
	{
            $this->is_profile = (Config::get('dx.employee_profile_page_url'));
	}
	
	/**
	 * Returns properly formatted string containing number of years of work anniversary.
	 *
	 * @param $join_date
	 * @return string
	 */
	private function getAnniversTxt($join_date)
	{
		$now = Carbon::now(Config::get('dx.time_zone'));
		$join = Carbon::createFromFormat('Y-m-d', $join_date);
		if($join->month == $now->month && $join->day == $now->day)
		{
			$yrs = $now->year - $join->year;
			$txt = trans('congratulate.lbl_anniversary');
			if($yrs == 0)
			{
				$txt = trans('congratulate.lbl_joined_today');
			}
			else
			{
				if($yrs == 1)
				{
					$txt .= $yrs . " " . trans('congratulate.lbl_year');
				}
				else
				{
					$txt .= $yrs . " " . trans('congratulate.lbl_years');
				}
			}
			return $txt;
		}
	}
}

?>