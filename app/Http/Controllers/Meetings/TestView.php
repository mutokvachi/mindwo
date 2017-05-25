<?php

namespace App\Http\Controllers\Meetings;

use App\Http\Controllers\Controller;
use DB;
use App\Exceptions; 
use Auth;
use Illuminate\Http\Request;

/**
 * Reports groups controller
 */
class TestView extends Controller
{
   
    public function test(Request $request) {
        $this->validate($request, [
            'view_id' => 'required'
        ]);
        $view_id = $request->input('view_id');
        
        $htm = \App\Http\Controllers\GridController::getViewEditFormHTML($request);
        return  view('meetings.test', [
            'htm' => $htm,
            'view_id' => $view_id,
            'operations' => DB::table('dx_field_operations')->orderBy('title')->get()
        ]);
    }
}
