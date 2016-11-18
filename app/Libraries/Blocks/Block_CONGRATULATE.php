<?php

namespace App\Libraries\Blocks;

use App;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Class Block_CONGRATULATE
 *
 * Widget that displays birthdays and work anniversaries
 *
 * @package App\Libraries\Blocks
 */
class Block_CONGRATULATE extends Block
{
	protected $employees;
	
	public function getHtml()
	{
		$result = view('blocks.widget_congratulate', [
			'self' => $this,
			'employees' => $this->getEmployees()
		])->render();
		
		return $result;
	}
	
	public function getTypeOfEvent($employee)
	{
                if ($employee->birth_date && !$employee->join_date) {
                    return 'Birthday';
                }
            
		$now = Carbon::now();
		
		$birth = Carbon::createFromFormat('Y-m-d', $employee->birth_date);
		
		if($birth->month == $now->month && $birth->day == $now->day)
		{
			return 'Birthday';
		}
		
		else
		{
			$join = Carbon::createFromFormat('Y-m-d', $employee->join_date);
                        if($join->month == $now->month && $join->day == $now->day)
			{
				return 'Work anniversary - ' . ($now->year - $join->year) . ' year(s)';
			}
		}
	}
	
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
	
	protected function getEmployees()
	{
		if($this->employees)
		{
			return $this->employees;
		}
		
		$now = Carbon::now();
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
		// TODO: Implement parseParams() method.
	}
}

?>