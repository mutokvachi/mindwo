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
        $subject = \App\Models\Education\Subject::find($id);

        return view('pages.education.course.course', [
                    'subject' => $subject
                ])->render();
    }
}