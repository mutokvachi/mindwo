<?php

namespace App\Http\Controllers\Widgets;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests;
use DB;

/**
 * Controlls calendar widget. Returns events for calendar.
 */
class EmployeeCountController extends Controller
{

    /**
     * Gets all evenets in specified data range
     * @param Request $request Requests data containing filter parameters
     * @return array Events which are filtered
     */
    public function getView(Request $request)
    {
        $this->validate($request, [
            'widget_name' => 'required',
            'date' => 'required|date'
        ]);

        // Retrieve filters date
        $date = $request->input('date');
        
        $widget_name = $request->input('widget_name');
        
        $widget = \App\Libraries\Blocks\EmployeeCount\EmployeeCountFactory::initializeWidget($widget_name);
         
         return $widget->getViewUpdate($date);
    }
}