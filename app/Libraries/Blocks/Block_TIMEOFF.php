<?php

namespace App\Libraries\Blocks;

use DB;

use App;
use Illuminate\Support\Facades\Auth;
use App\Libraries\DBHelper;

/**
 * Widget displays current user timeoff balance and provides possibility to request leave
 */
class Block_TIMEOFF extends Block
{	
        /**
         * Leaves request register field for user_id
         * @var integer 
         */
        public $user_field_id = 0;
        
        /**
         * Leaves request register id
         * @var type 
         */
        public $leaves_list_id = 0;
        
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
            
		return <<<END
			<script>
				$(document).ready(function(){
					$(".dx-timeoff-balance .dx-btn-leave-request").click(function() {   
                                            show_page_splash();
                                            view_list_item("form", 0, $(this).data('leaves-list-id'), $(this).data('user-field-id'), $(this).data('user-id'), "", ""); 
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
            $this->leaves_list_id = DBHelper::getListByTable("dx_users_left")->id;
            $this->user_field_id = DB::table('dx_lists_fields')
                             ->where('list_id', '=', $this->leaves_list_id)
                             ->where('db_name', '=', 'user_id')
                             ->first()->id;
	}
}

?>