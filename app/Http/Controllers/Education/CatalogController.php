<?php

namespace App\Http\Controllers\Education;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        $courses = [];

        $courses[] = (object)[
            'id' => 3,
            'icon' => 'fa fa-briefcase',
            'title' => 'Jaunais Publisko iepirkumu likums - iesācējiem',
            'date' => new \DateTime('2017-08-21'),
            'time_from' => '12:30',
            'time_to' => '17:00',
            'is_full' => false
        ];

        $courses[] = (object)[
            'id' => 2,
            'icon' => 'fa fa-university',
            'title' => 'Jautājumu uzdošana un atbilžu sniegšana formālās situācijās angļu valodā',
            'date' => new \DateTime('2017-09-05'),
            'time_from' => '15:00',
            'time_to' => '19:30',
            'is_full' => true
        ];

        $courses[] = (object)[
            'id' => 2,
            'icon' => 'fa fa-university',
            'title' => 'Jautājumu uzdošana un atbilžu sniegšana formālās situācijās angļu valodā',
            'date' => new \DateTime('2017-09-10'),
            'time_from' => '10:30',
            'time_to' => '15:00',
            'is_full' => false
        ];

        return view('pages.education.catalog', [
                    'courses' => $courses
                ])->render();
    }
}