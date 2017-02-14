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
         * Shows how many days back and forward must be shown 
         * @var int 
         */
        public $show_days_interval = 0;
        
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
             $birth = $employee->birth_date ? Carbon::createFromFormat('Y-m-d', $employee->birth_date)->format('m.d') : '';
            $join =$employee->join_date ? Carbon::createFromFormat('Y-m-d', $employee->join_date)->format('m.d') : '';
            
		if($employee->birth_date && !$employee->join_date)
		{
			return trans('congratulate.lbl_birthday') . ($this->show_days_interval > 0 ? ' (' . $birth . ')' : '');
		}
		
		if(!$employee->birth_date && $employee->join_date)
		{
			return $this->getAnniversTxt($employee->join_date) . ($this->show_days_interval > 0 ? ' (' . $join . ')' : '');
		}
		
		$now = Carbon::now(Config::get('dx.time_zone'));
		
		
                
                $start = new \DateTime($now->subDays($this->show_days_interval)->toDateString()); 
                  $end = new \DateTime($now->addDays($this->show_days_interval*2)->toDateString()); 
                  
                 $arr_emp = [$employee]; 
                  
                $emp1 = $this->getBirthdaysInRange($arr_emp , 'birth_date',  $start, $end);
		
		if(count($emp1) > 0)
		{
			return trans('congratulate.lbl_birthday') . ($this->show_days_interval > 0 ? ' (' . $birth . ')' : '');
		}
		
		else
		{
			return $this->getAnniversTxt($employee->join_date). ($this->show_days_interval > 0 ? ' (' . $join . ')' : '');
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
     * Filters birthdays in specified date range
     * @param object $res Results with user data from data base
     * @param string $start Filter starting date in string format
     * @param string $end Filter ending date in string format
     * @return array Filtered results
     */
    private function getBirthdaysInRange($res, $date_col,  $start, $end)
    {
        $new_res = array();

        foreach ($res as $record) {
            if (!$record->$date_col) {
                continue;
            }

            $birthday = new \DateTime($record->$date_col);

            $diffYears = ($start->format("Y") - $birthday->format("Y"));

            if ($diffYears > 0) {
                $temp = $birthday->add(new \DateInterval('P' . $diffYears . 'Y'));
            } else {
                $temp = $birthday->sub(new \DateInterval('P' . ($diffYears * -1) . 'Y'));
            }

            if ($temp < $start) {
                $temp->add(new \DateInterval('P1Y'));
            }

            if ($birthday <= $end && $temp >= $start && $temp <= $end) {
                $record->$date_col = $temp->format("Y-m-d");

                $new_res[] = $record;
            }
        }

        return $new_res;
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
		
		$this->employees = App\User::all()/*where(function ($query) use ($now)
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
			->get()*/
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
                        
                  $start = new \DateTime($now->subDays($this->show_days_interval)->toDateString()); 
                  $end = new \DateTime($now->addDays($this->show_days_interval*2)->toDateString()); 
                        
                $emp1 = $this->getBirthdaysInRange($this->employees , 'birth_date',  $start, $end);
                $emp2 = $this->getBirthdaysInRange($this->employees , 'join_date',  $start, $end);
               
                \Log::info('res:'. count($emp1) . ' res2:' . count($emp2));
                
                $emps = (object)array_merge($emp1, $emp2);
		
		return $emps;
	}
	
	protected function parseParams()
	{
            $this->is_profile = (Config::get('dx.employee_profile_page_url'));
            
            $dat_arr = explode('|', $this->params);

            foreach ($dat_arr as $item) {
                $val_arr = explode('=', $item);

                if ($val_arr[0] == "SHOW_DAYS_INTERVAL") {
                    $this->show_days_interval = getBlockParamVal($val_arr);
                }  else if (strlen($val_arr[0]) > 0) {
                    throw new PagesException("Norādīts blokam neatbilstošs parametra nosaukums (" . $val_arr[0] . ")!");
                }
            }
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