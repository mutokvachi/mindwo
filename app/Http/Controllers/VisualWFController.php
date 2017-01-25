<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Webpatser\Uuid\Uuid;

use App\Libraries\Rights;
use App\Libraries\FieldsHtm;
use App\Libraries\DataView;

use App\Exceptions;
use Auth;
use DB;
use Config;
use Hash;
use Log;

class VisualWFController extends Controller
{
    public function getWFForm(Request $request) {
        $this->validate($request, [
            'item_id' => 'required|integer|exists:dx_workflows_def,id'
        ]);
        
        $item_id = $request->input('item_id');
        
        $grid_htm_id = $request->input('grid_htm_id', '');
        $frm_uniq_id = Uuid::generate(4);
        $is_disabled = 1; //read-only rights by default
        
        $form_htm = view('workflow.visual_ui.wf_form', [
                'frm_uniq_id' => $frm_uniq_id, 
                'form_title' => trans('task_form.form_title'),
                'is_disabled' => $is_disabled,                    
                'grid_htm_id' => $grid_htm_id,
                'item_id' => $item_id,
                'json_model' => $this->getWFJson($item_id)
                ])->render();
        
        return response()->json(['success' => 1, 'html' => $form_htm]);
    }
    
    private function getWFJson($item_id) {
        return <<<END
        { "class": "go.GraphLinksModel",
            "linkFromPortIdProperty": "fromPort",
            "linkToPortIdProperty": "toPort",
            "nodeDataArray": [ 
          {"category":"Comment", "loc":"360 -10", "text":"Kookie Brittle", "key":-13},
          {"key":-1, "category":"Start", "loc":"175 0", "text":"Start"},
          {"key":0, "loc":"0 77", "text":"Preheat oven to 375 F"},
          {"key":1, "loc":"175 100", "text":"In a bowl, blend: 1 cup margarine, 1.5 teaspoon vanilla, 1 teaspoon salt"},
          {"key":2, "loc":"175 190", "text":"Gradually beat in 1 cup sugar and 2 cups sifted flour"},
          {"key":3, "loc":"175 270", "text":"Mix in 6 oz (1 cup) Nestle's Semi-Sweet Chocolate Morsels"},
          {"key":4, "loc":"175 370", "text":"Press evenly into ungreased 15x10x1 pan"},
          {"key":5, "loc":"352 85", "text":"Finely chop 1/2 cup of your choice of nuts"},
          {"key":6, "loc":"175 440", "text":"Sprinkle nuts on top"},
          {"key":7, "loc":"175 500", "text":"Bake for 25 minutes and let cool"},
          {"key":8, "loc":"175 570", "text":"Cut into rectangular grid"},
          {"key":-2, "category":"End", "loc":"-101 623.9999999999999", "text":"Enjoy!"},
          {"category":"End", "text":"End", "key":-4, "loc":"527 406.25"}
           ],
            "linkDataArray": [ 
          {"from":1, "to":2, "fromPort":"B", "toPort":"T", "points":[175,139.7,175,149.7,175,149.7,175,148.1,175,148.1,175,158.1]},
          {"from":2, "to":3, "fromPort":"B", "toPort":"T", "points":[175,221.9,175,231.9,175,231.9,175,228.1,175,228.1,175,238.1]},
          {"from":3, "to":4, "fromPort":"B", "toPort":"T", "points":[175,301.9,175,311.9,175,320,175,320,175,328.1,175,338.1]},
          {"from":4, "to":6, "fromPort":"B", "toPort":"T", "points":[175,401.90000000000003,175,411.90000000000003,175,412.8,175,412.8,175,413.7,175,423.7]},
          {"from":6, "to":7, "fromPort":"B", "toPort":"T", "points":[175,456.3,175,466.3,175,466.3,175,465.9,175,465.9,175,475.9]},
          {"from":7, "to":8, "fromPort":"B", "toPort":"T", "points":[175,524.1,175,534.1,175,535,175,535,175,535.9,175,545.9]},
          {"from":8, "to":-2, "fromPort":"B", "toPort":"T", "points":[175,594.1,175,604.1,14.351744186046538,604.1,14.351744186046538,580.796511627907,-100.99999999999994,580.796511627907,-100.99999999999994,590.796511627907]},
          {"from":-1, "to":0, "fromPort":"B", "toPort":"T", "points":[175,25.209302325581397,175,35.2093023255814,175,42.9546511627907,0,42.9546511627907,0,50.7,0,60.7]},
          {"from":-1, "to":1, "fromPort":"B", "toPort":"T", "points":[175,25.209302325581397,175,35.2093023255814,175,42.754651162790694,175,42.754651162790694,175,50.3,175,60.3]},
          {"from":-1, "to":5, "fromPort":"B", "toPort":"T", "points":[175,25.209302325581397,175,35.2093023255814,175,43.0546511627907,352,43.0546511627907,352,50.9,352,60.9]},
          {"from":5, "to":4, "fromPort":"B", "toPort":"T", "points":[352,109.1,352,119.1,352,116,352,116,352,308,175,308,175,328.1,175,338.1]},
          {"from":0, "to":4, "fromPort":"B", "toPort":"T", "points":[0,93.30000000000001,0,103.30000000000001,0,100,0,100,0,308,175,308,175,328.1,175,338.1]},
          {"from":4, "to":-4, "fromPort":"R", "toPort":"T", "points":[250.5,370,260.5,370,527,370,527,372.7005813953489,527,375.4011627906977,527,385.4011627906977]}
           ]}
END;
    }

}