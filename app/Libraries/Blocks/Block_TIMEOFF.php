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
	/**
	 * Render widget and return its HTML.
	 *
	 * @return string
	 */
	public function getHtml()
	{
		$result = view('blocks.timeoff.widget', [
			'self' => $this,
                        'user' => App\User::find(Auth::user()->id)
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
}

?>