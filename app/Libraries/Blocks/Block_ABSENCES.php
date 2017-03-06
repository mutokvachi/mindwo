<?php

namespace App\Libraries\Blocks;

use App;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Config;
use DB;
use Log;

/**
 * Class Block_ABSENCES
 *
 * Widget that displays employees which are absent
 *
 * @package App\Libraries\Blocks
 */
class Block_ABSENCES extends Block
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
		$result = view('blocks.widget_absences', [
			'self' => $this,
			'employees' => $this->getEmployees()
		])->render();
		
		return $result;
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
					var items = $('.widget-absences .mt-actions > .mt-action');
					var mult = (items.length < 3 ? items.length : 3);
					$('.widget-absences .mt-actions').slimScroll({
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
				.widget-absences .mt-action-img img {
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
	 * Find employees that are left
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
		
		$this->employees = App\User::whereNotNull('left_to')->get();
                
		return $this->employees;
	}
	
	protected function parseParams()
	{
            $this->is_profile = (Config::get('dx.employee_profile_page_url'));
	}
}