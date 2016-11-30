<?php

namespace App\Libraries\Blocks;

use DB;

use App;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Widget displays current user timeoff balance and provides possibility to request leave
 */
class Block_TIMEOFF extends Block
{
	private $work_day_h = 8;
	
	/**
	 * Render widget and return its HTML.
	 *
	 * @return string
	 */
	public function getHtml()
	{
		$result = view('blocks.timeoff.widget', [
			'self' => $this
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
            return "";
            /*
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
             
             */
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
                                .widget-timeoff .sale-summary {
                                        box-shadow: none!important;
                                }
			</style>
END;
             
	}
	
	public function getJSONData()
	{	
	}
        
	protected function parseParams()
	{
	}
        
        public function getTimeoffs() {
            $timeoffs =  DB::table('dx_timeoff_types as to')
                         ->where('to.is_disabled', '=', 0)
                         ->get();
            
            foreach($timeoffs as $timeoff) {
                $balance = DB::table('dx_timeoff_calc')
                           ->where('user_id', '=', Auth::user()->id)
                           ->where('timeoff_type_id', '=', $timeoff->id)
                           ->orderBy('calc_date', 'DESC')
                           ->first();
                
                $timeoff->unit = "h";
                $time = ($balance) ? $balance->balance : 0;
                if (!$timeoff->is_accrual_hours) {
                    $time = round(($time/$this->work_day_h));
                    $timeoff->unit = "d";
                }
                
                $timeoff->balance = $time;
            }
            
            return $timeoffs;
                   
        }
}

?>