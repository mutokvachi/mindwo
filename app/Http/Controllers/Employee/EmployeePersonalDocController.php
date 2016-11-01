<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

use Log;

/**
 * Employee's personal documents controller
 */
class EmployeePersonalDocController extends Controller
{
    public function testView(){
        return view('pages.employees_doc_test');
    }
    
    public function getPersonalDocsByCountry($country_id){
        $country  = \App\Models\Country::find($country_id)->first();
        
        return json_encode($country->employeePersonalDocs());
    }
}
