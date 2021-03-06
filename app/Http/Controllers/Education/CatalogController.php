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
        $tags = $request->input('tag');
        $programs = $request->input('program');
        $modules = $request->input('module');
        $teachers = $request->input('teacher');
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');
        $time_from = $request->input('time_from');
        $time_to = $request->input('time_to');
        $only_free = $request->input('only_free');
        $show_full = $request->input('show_full');

        $query = DB::table('edu_subjects AS sub')
            ->selectRaw(DB::raw('sub.id,
                sub.title_full AS title, 
                SUM((SELECT COUNT(*) FROM edu_subjects_groups_members grm WHERE grm.group_id = gr.id) < gr.seats_limit) as is_not_full,
                MIN(grd.lesson_date) min_lesson_date,
                MAX(grd.lesson_date) max_lesson_date,
                COUNT(gr.id) group_count'
                ))

            ->leftJoin('edu_subjects_groups AS gr', 'sub.id', '=', 'gr.subject_id')
            ->leftJoin('edu_subjects_groups_days AS grd', 'gr.id', '=', 'grd.group_id')

            ->leftJoin('edu_subjects_tags AS ta', 'ta.subject_id', '=', 'sub.id') //tag filtr
            ->leftJoin('edu_tags AS tag', 'ta.tag_id', '=', 'tag.id') //tag filtr

            ->leftJoin('edu_modules AS m', 'm.id', '=', 'sub.module_id') //prog
            ->leftJoin('edu_programms AS pr', 'pr.id', '=', 'm.programm_id') //prog

            ->leftJoin('edu_subjects_teachers AS te', 'te.subject_id', '=', 'sub.id') //teach  
            ->leftJoin('dx_users AS u', 'te.teacher_id', '=', 'u.id') //teach            

            ->where('sub.is_published', 1) // Only published    
            ->where(function ($query) {
                $query->where(function ($query) {
                        $query->where('gr.is_published', 1);
                        $query->orWhereNull('gr.id');                
                    });      
                $query->where(function ($query) {
                        $query->where('gr.signup_due', '>=', Carbon::today()->toDateString());
                        $query->orWhereNull('gr.signup_due');   
                        $query->orWhereNull('gr.id');                 
                    });          
            }) 
            

            ->groupBy('sub.id'); // Signup date larger than today

        if($text && strlen(trim($text)) > 0){
            $query->where(function ($query) use ($text) {
               $query->where('sub.title_full', 'like', '%' . $text . '%');
               $query->orWhere('gr.title', 'like', '%' . $text . '%');
               $query->orWhere('tag.title', 'like', '%' . $text . '%');
               $query->orWhere('m.title_full', 'like', '%' . $text . '%');
               $query->orWhere('pr.title', 'like', '%' . $text . '%');
               $query->orWhere('u.display_name', 'like', '%' . $text . '%');
            });
        }

        // Filter by tags
        if ($tags && count($tags) > 0) { 
            $query->where(function ($query) use ($tags) {
                foreach ($tags as $tag) {
                    $query->orWhere('ta.tag_id', '=', $tag);
                }
            });
        }

        // Filter by programms
        if ($programs && count($programs) > 0) {
            $query->where(function ($query) use ($programs) {
                foreach ($programs as $program) {
                    $query->orWhere('m.programm_id', '=', $program);
                }
            });
        }

        // Filter by modules
        if ($modules && count($modules) > 0) {
            $query->where(function ($query) use ($modules) {
                foreach ($modules as $module) {
                    $query->orWhere('sub.module_id', '=', $module);
                }
            });
        }

        // Filter by teachers
        if ($teachers && count($teachers) > 0) {
            
            $query->where(function ($query) use ($teachers) {
                foreach ($teachers as $teacher) {
                    $query->orWhere('te.teacher_id', '=', $teacher);
                }
            });
        }

        if ($date_from || $date_to || $time_from || $time_to) { 
            if ($date_from) {
                $query->where(function ($query) use ($date_from) {
                    $query->where('grd.lesson_date', '>=', $date_from);
                });
            }

            if ($date_to) {
                 $query->where(function ($query) use ($date_to) {
                    $query->where('grd.lesson_date', '<=', $date_to);
                 });
            }

            if ($time_from) {
                $query->where('grd.time_from', '>=', $time_from);
            }

            if ($time_to) {
                $query->where('grd.time_from', '<=', $time_to);
            }
        }

        // If true show only which are free
        if ($only_free == 1) {
            $query->where('sub.price_for_student', '<=', 0);
        }

        // If false then don't show those which are full
        if ($show_full == 0) {
            $query->whereRaw('(SELECT COUNT(*) FROM edu_subjects_groups_members grm WHERE grm.group_id = gr.id) < gr.seats_limit');
        }

        $res = $query->get();

        $html = view('pages.education.catalog_body', [
            'results' => $res
        ])->render();

        return response()->json(['success' => 1, 'html' => $html]);
    }
}
