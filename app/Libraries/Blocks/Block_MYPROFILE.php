<?php

namespace App\Libraries\Blocks;

use App;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Widget displays current user timeoff balance and provides possibility to request leave
 */
class Block_MYPROFILE extends Block
{
	protected $employees;
	
	/**
	 * Render widget and return its HTML.
	 *
	 * @return string
	 */
	public function getHtml()
	{
		$result = view('blocks.widget_profile', [
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
				.widget-profile .mt-img img {
					width: 100px;
					height: 100px;
				}
            
                                .widget-profile .mt-img {
                                        margin-top: 20px!important;
                                        margin-bottom: 10px!important;
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