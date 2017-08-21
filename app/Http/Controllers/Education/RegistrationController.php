<?php

namespace App\Http\Controllers\Education;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Education registration controller
 */
class RegistrationController extends Controller
{
    /**
     * Retrieves registration view
     *
     * @return void
     */
    public function getView($id = 0)
    {
        $availableOpenGroups = \App\Models\Education\SubjectGroup::where('edu_subjects_groups.is_published', 1)
            ->whereRaw('(SELECT COUNT(*) FROM edu_subjects_groups_members grm WHERE grm.group_id = edu_subjects_groups.id) < seats_limit')
            ->where(function ($query) {
                $query->where('edu_subjects_groups.signup_due', '>=', Carbon::today()->toDateString());
                $query->orWhereNull('edu_subjects_groups.signup_due');
            })
            ->get();

        return view('pages.education.registration.registration', [
                    'course' => $id  == 0 ? false : false,
                    'availableOpenGroups' => $availableOpenGroups
                ])->render();
    }
}