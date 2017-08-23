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

    public function saveFeedback(Request $request)
    {
        \Log::info('sakums');
        $author = $request->input('author');
        $email = $request->input('email');
        $text = $request->input('text');
        $subject_id = $request->input('subject_id');

        $subject = \App\Models\Education\Subject::find($subject_id);

        if(!$subject){
            return response()->json(['success' => 0]);
        }

        $feedback = new \App\Models\Education\SubjectFeedback();

        $feedback->author = $author;
        $feedback->email = $email;
        $feedback->text = $text;
        $feedback->subject_id =  $subject->id;
        $feedback->modified_time = new \DateTime();
        $feedback->created_time = new \DateTime();

        $feedback->save();

        return response()->json(['success' => 1]);
    }
}
