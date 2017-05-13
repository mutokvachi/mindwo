<?php

namespace App\Http\Controllers\Constructor;

use App\Http\Controllers\Controller;
use DB;
use App\Exceptions; 
use Auth;
use Illuminate\Http\Request;

/**
 * Registers UI constructor controller
 */
class RegisterController extends Controller
{
    
    /**
     * Current register ID beeing processed
     * @var integer
     */
    public $list_id = 0;
      
    /**
     * Get constructor UI page for new register creation
     * @return Response
     */
    public function getNewConstructor() {
        
        // check rights - user must be in role (role ID in config/dx.php - create parameter)
        // if no rights then showNoRightsError
        
        return redirect()->route('register_constructor', ['list_id' => 0]);        
    }
    
    /**
     * Get constructor UI page for register editing
     * @return Response
     */
    public function getEditConstructor($list_id) {
        $this->list_id = $list_id; // if 0 then new list
        
        // check rights - user must be in role (role ID in config/dx.php - create parameter)
        
        // register meta data is in db table dx_lists
        
        return view('constructor.index', ['self' => $this]);
    }
    
    
    /**
     * Render error page with no rights message
     * @return Response
     */
    private function showNoRightsError() {
        return  view('errors.attention', [
                    'page_title' => trans('errors.access_denied_title'),
                    'message' => trans('errors.no_rights_on_meetings') // create here translation
		]);
    }

}
