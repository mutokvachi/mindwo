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

        $subject_id = 0;
        if ($id > 0) {
            $subject = \App\Models\Education\Subject::find($id);

            if ($subject) {
                $subject_id = $id;
            }
        }

        return view('pages.education.registration.registration', [
                    'course' => $id  == 0 ? false : false,
                    'availableOpenGroups' => $availableOpenGroups,
                    'is_coordinator' => false,
                    'subject_id' => $subject_id
                ])->render();
    }

    public function getGroup($id)
    {
        $group = \App\Models\Education\SubjectGroup::with('subject')->find($id);

        $groupStartDay = $group->firstDay();
        if ($groupStartDay) {
            $groupStartDate = date_create($groupStartDay->lesson_date)->format('d.m.Y');
        } else {
            $groupStartDate = false;
        }

        $groupEndDay = $group->lastDay();
        if ($groupEndDay) {
            $groupEndDate = date_create($groupStartDay->lesson_date)->format('d.m.Y');
        } else {
            $groupEndDate = false;
        }

        return response()->json(['success' => 1,
            'group' => $group,
            'group_start' => $groupStartDate,
            'group_end' => $groupEndDate
            ]);
    }

    public function save(Request $request)
    {
        $groups = $request->input('groups');

       \Log::info('Grupas: ' . print_r($groups, true));

        $invoice = new  \App\Models\Education\Invoice();
        $invoice->type = $request->input('type');
        $invoice->name = $request->input('name');
        $invoice->address = $request->input('address');
        $invoice->regnr = $request->input('regnr');
        $invoice->bank = $request->input('bank');
        $invoice->swift = $request->input('swift');
        $invoice->account = $request->input('account');
        $invoice->email = $request->input('email');
        $invoice->modified_time = new \DateTime();
        $invoice->created_time = new \DateTime();
        $invoice->save();

        foreach ($groups as $groupRow) {
            \Log::info('iteracija gr: ');
            if (!$groupRow || !is_array($groupRow)) {
                continue;
            }

            $group = \App\Models\Education\SubjectGroup::find($groupRow['group_id']);

            if (!$group) {
                continue;
            }

            if (array_key_exists('participants', $groupRow)) {
                foreach ($groupRow['participants'] as $participant) {
                    \Log::info('iteracija part: ');
                    $user = \App\User::where('person_code', $participant['pers_code'])->first();

                    if (!$user) {
                        $user = new \App\User();
                        $user->person_code = $participant['pers_code'];
                        $user->display_name = $participant['name'] . ' ' . $participant['lastname'];
                        $user->first_name = $participant['name'];
                        $user->last_name = $participant['lastname'];
                        //$user->pers_code = $participant['job'];
                        //$user->pers_code = $participant['position'];
                        $user->phone = $participant['telephone'];
                        $user->email = $participant['email'];
                        $user->save();
                    }

                    $member = new \App\Models\Education\SubjectGroupMember();
                    $member->group_id = $group->id;
                    $member->student_id = $user->id;
                    $member->invoice_id = $invoice->id;
                    $member->modified_time = new \DateTime();
                    $member->created_time = new \DateTime();
                    $member->save();
                }
            }
        }

        return response()->json(['success' => 1]);
    }
}
