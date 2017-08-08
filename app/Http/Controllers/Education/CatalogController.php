<?php

namespace App\Http\Controllers\Education;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Education;
use DB;
use Carbon\Carbon;

/**
 * Education catalog controller
 */
class CatalogController extends Controller
{
    /**
     * Retrieves catalog view
     *
     * @return void
     */
    public function getView()
    {
        /*$courses = [];

        $courses[1] = (object)[
            'id' => 1,
            'icon' => 'fa fa-briefcase',
            'title' => 'Jaunais Publisko iepirkumu likums - iesācējiem',
            'date' => new \DateTime('2017-08-21'),
            'time_from' => '12:30',
            'time_to' => '17:00',
            'is_full' => false
        ];

        $courses[2] = (object)[
            'id' => 2,
            'icon' => 'fa fa-university',
            'title' => 'Jautājumu uzdošana un atbilžu sniegšana formālās situācijās angļu valodā',
            'date' => new \DateTime('2017-09-05'),
            'time_from' => '15:00',
            'time_to' => '19:30',
            'is_full' => true
        ];

        $courses[3] = (object)[
            'id' => 3,
            'icon' => 'fa fa-university',
            'title' => 'Jautājumu uzdošana un atbilžu sniegšana formālās situācijās angļu valodā',
            'date' => new \DateTime('2017-09-10'),
            'time_from' => '10:30',
            'time_to' => '15:00',
            'is_full' => false
        ];*/

        return view('pages.education.catalog')->render();
    }

    public function getData(Request $request)
    {
        $text = $request->input('text');
        $tag = $request->input('tag');
        $program = $request->input('program');
        $module = $request->input('module');
        $teacher = $request->input('teacher');
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');
        $time_from = $request->input('time_from');
        $time_to = $request->input('time_to');
        $only_free = $request->input('only_free');
        $show_full = $request->input('show_full');

       // $groups = 

       $query = DB::table('edu_subjects AS sub')
            ->selectRaw(DB::raw('sub.id AS sub_id, gr.id AS gr_id, MIN(IFNULL(gr.title, sub.title)) AS title'))
            ->join('edu_subjects_groups AS gr', 'sub.id', '=', 'gr.subject_id')            
            ->where('sub.is_published', 1) // Only published
            ->where('gr.signup_due', '>=', Carbon::today()->toDateString())
            ->groupBy('sub.id, gr.id'); // Signup date larger than today

        if($date_from || $date_to || $time_from || $time_to) {
            $query->join('edu_subjects_groups_days AS grd', 'gr.id', '=', 'grd.group_id');

            if($date_from){
                $query->where('grd.lesson_date', '>=', $date_from);
            }

            if($date_to){
                $query->where('grd.lesson_date', '<=', $date_to);
            }

          /*  if($time_from){
                $query->where('grd.time_from', '>=', $time_from);
            }

            if($time_to){
                $query->where('grd.time_from', '<=', $time_to);
            }*/
        }

        // If true show only which are free
        if($only_free == 1){
            $query->where('sub.is_fee', 0);
        }

        // If false then don't show those which are full
        if($show_full == 0){
            $query->whereRaw('(SELECT COUNT(*) FROM edu_subjects_groups_members grm WHERE grm.group_id = gr.id) < gr.seats_limit');
        }

        $res = $query->get();  

        /*$groups = [];

       foreach($res as $row){
            if(!array_key_exists($row->id, $groups)){
                $groups[$row->id] = [];
            }

            $groups[$row->id][] = $row;
        }*/



        /*$group_ids =[];

        foreach($res as $row){
            $group_ids[] = $row->id;
        }

        $groups = Education\SubjectGroup::find($group_ids);*/

        $html = view('pages.education.catalog_body', [
            'results' => $res
        ])->render();

        return response()->json(['success' => 1, 'html' => $html]);
    }
}
