<?php

namespace App\Http\Controllers\Education;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Education course controller
 */
class CourseController extends Controller
{
    /**
     * Retrieves course view
     *
     * @return void
     */
    public function getView($id)
    {
        $courses = [];

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
        ];

        return view('pages.education.course.course', [
                    'course' => $courses[$id]
                ])->render();
    }
}