<?php

namespace App\Libraries\Blocks;

use App;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Config;
use DB;
use Log;

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
			return $this->getBirthdayTxt($employee->birth_date);
		}
		
		if(!$employee->birth_date && $employee->join_date)
		{
			return $this->getAnniversTxt($employee->join_date);
		}
		
		$now = Carbon::now(Config::get('dx.time_zone'));
		
		$birth = Carbon::createFromFormat('Y-m-d', $now->year . Carbon::createFromFormat('Y-m-d', $employee->birth_date)->format('-m-d'));
                
		if(abs($now->diffInDays($birth)) < 3)
		{
                    return $this->getBirthdayTxt($employee->birth_date);                                       
		}		
		else
		{
                    return $this->getAnniversTxt($employee->join_date);
		}
	}
        
        private function getDateInfo($birth_date, $join_date) {
            if($birth_date && !$join_date)
            {
                return Carbon::createFromFormat('Y-m-d', $birth_date)->format("m-d");
            }

            if(!$birth_date && $join_date)
            {
                return Carbon::createFromFormat('Y-m-d', $join_date)->format("m-d");
            }

            $now = Carbon::now(Config::get('dx.time_zone'));

            $birth = Carbon::createFromFormat('Y-m-d', $now->year . Carbon::createFromFormat('Y-m-d', $birth_date)->format('-m-d'));

            if(abs($now->diffInDays($birth)) < 3)
            {
                return Carbon::createFromFormat('Y-m-d', $birth_date)->format("m-d");                                
            }		
            else
            {
                return Carbon::createFromFormat('Y-m-d', $join_date)->format("m-d");  
            }
        }
        
        /**
         * Returns birthay text and date (day and month)
         * 
         * @param DateTime $birth_date Birthday in db date format
         * @return string Birthday text
         */
        private function getBirthdayTxt($birth_date) {
            $now = Carbon::now(Config::get('dx.time_zone'));
            $birth = Carbon::createFromFormat('Y-m-d', $birth_date);
            
            if ($birth->month == $now->month && $birth->day == $now->day) {
                $txt = "<font color=red>" . trans('congratulate.lbl_birthday') . " " . trans('congratulate.lbl_today') . "</font>";
            }
            else {
                $txt = trans('congratulate.lbl_birthday') . " " . $birth->format("d M");
            }
            return $txt;
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
                        $now1 = $now->copy()->addDay();
                        $query
                                ->whereMonth('birth_date', '=', $now1->month)
                                ->whereDay('birth_date', '=', $now1->day);
                })                
                ->orWhere(function ($query) use ($now)
                {
                        $now2 = $now->copy()->addDays(2);
                        $query
                                ->whereMonth('birth_date', '=', $now2->month)
                                ->whereDay('birth_date', '=', $now2->day);
                })
                ->orWhere(function ($query) use ($now)
                {
                        $now_1 = $now->copy()->subDay();
                        
                        $query
                                ->whereMonth('birth_date', '=', $now_1->month)
                                ->whereDay('birth_date', '=', $now_1->day);
                })
                ->orWhere(function ($query) use ($now)
                {
                        $now_2 = $now->copy()->subDays(2);
                        $query
                                ->whereMonth('birth_date', '=', $now_2->month)
                                ->whereDay('birth_date', '=', $now_2->day);
                })                
                ->orWhere(function ($query) use ($now)
                {
                        $query
                                ->whereMonth('join_date', '=', $now->month)
                                ->whereDay('join_date', '=', $now->day);
                })
                ->orWhere(function ($query) use ($now)
                {
                        $now1 = $now->copy()->addDay();
                        $query
                                ->whereMonth('join_date', '=', $now1->month)
                                ->whereDay('join_date', '=', $now1->day);
                })                
                ->orWhere(function ($query) use ($now)
                {
                        $now2 = $now->copy()->addDays(2);
                        $query
                                ->whereMonth('join_date', '=', $now2->month)
                                ->whereDay('join_date', '=', $now2->day);
                })
                ->orWhere(function ($query) use ($now)
                {
                        $now_1 = $now->copy()->subDay();
                        $query
                                ->whereMonth('join_date', '=', $now_1->month)
                                ->whereDay('join_date', '=', $now_1->day);
                })
                ->orWhere(function ($query) use ($now)
                {
                        $now_2 = $now->copy()->subDays(2);
                        $query
                                ->whereMonth('join_date', '=', $now_2->month)
                                ->whereDay('join_date', '=', $now_2->day);
                })                
                ->get();
                
                $srt = array();
                $empl_arr = array();
                foreach ($this->employees as $key => $row)
                {
                    $srt[$key] = $this->getDateInfo($row->birth_date, $row->join_date);
                    $empl_arr[$key] = $row;
                }
                                
                array_multisort($srt, SORT_ASC, $empl_arr);
                
		return $empl_arr;
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
		
                $join_fix = Carbon::createFromFormat('Y-m-d', $now->year . Carbon::createFromFormat('Y-m-d', $join_date)->format('-m-d'));
                
                $day_dif = $now->diffInDays($join_fix);
                
                $join_fix2 = $join->copy()->addDays($day_dif);
                
                $yrs = $now->year - $join_fix2->year;
                $txt = trans('congratulate.lbl_anniversary');
                if($yrs == 0)
                {
                    if ($day_dif == 0) {    
                        $txt = "<font color=red>" . trans('congratulate.lbl_joined_today') . "</font>";
                    }
                    else {
                        $txt = trans('congratulate.lbl_will_join') . " " . $join->format("d M");
                    }
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
                        $txt .= ", " . $join->format("d M");
                }
                return $txt;
		
	}
}