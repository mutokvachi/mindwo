<?php

namespace App\Models\Education;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;

class Subject extends Model
{
     /**
     * @var string Related table
     */
    protected $table = 'edu_subjects';

    /**
     * @var bool Disables Laravel's time stamps on insert and update
     */
    public $timestamps = false;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_time',
        'modified_time'
    ];

    /**
     * Related education module
     *
     * @return \App\Models\Education\Module
     */
    public function module()
    {
        return $this->belongsTo('\App\Models\Education\Module', 'module_id');
    }

    /**
     * All teachers for specified subject
     *
     * @return \App\User
     */
    public function teachers()
    {
        return $this->belongsToMany('\App\User', 'edu_subjects_teachers', 'subject_id', 'teacher_id');
    }

    /**
     * Return all available published groups
     *
     * @return \App\Models\Education\SubjectGroup
     */
    public function avaliableGroups()
    {
        return $this->hasMany('\App\Models\Education\SubjectGroup', 'subject_id')
            ->where(function ($query) {
                $query->where('edu_subjects_groups.signup_due', '>=', Carbon::today()->toDateString());
                $query->orWhereNull('edu_subjects_groups.signup_due');
            })
            ->where('edu_subjects_groups.is_published', 1);
    }

    public function tags()
    {
        return $this->belongsToMany('\App\Models\Education\Tag', 'edu_subjects_tags', 'subject_id', 'tag_id');
    }

    public function avaliableOpenGroups()
    {
        return $this->hasMany('\App\Models\Education\SubjectGroup', 'subject_id')
            ->whereRaw('(SELECT COUNT(*) FROM edu_subjects_groups_members grm WHERE grm.group_id = edu_subjects_groups.id) < seats_limit')
            ->where(function ($query) {
                $query->where('edu_subjects_groups.signup_due', '>=', Carbon::today()->toDateString());
                $query->orWhereNull('edu_subjects_groups.signup_due');
            })
            ->where('edu_subjects_groups.is_published', 1);
    }

    /**
     * Gets info about availability is_not_full and group_count
     *
     * @return object
     */
    public function getAvailability()
    {
         $res = DB::table('edu_subjects AS sub')
            ->selectRaw(DB::raw('SUM((SELECT COUNT(*) FROM edu_subjects_groups_members grm WHERE grm.group_id = gr.id) < gr.seats_limit) as is_not_full,
                COUNT(gr.id) group_count'
                ))
            ->leftJoin('edu_subjects_groups AS gr', 'sub.id', '=', 'gr.subject_id')
            ->where(function ($query) {
                $query->where('gr.signup_due', '>=', Carbon::today()->toDateString());
                $query->orWhereNull('gr.signup_due');
            })
            ->where('sub.id', $this->id)
            ->groupBy('sub.id')
            ->first();

        return $res;
    }

    /**
     * Subject coordinator
     * @return \App\User
     */
    public function coordinator()
    {
        return $this->belongsTo('\App\Models\Education\OrgUser', 'coordinator_id', 'user_id');
    }

    /**
     * User who last created record
     * @return \App\User
     */
    public function createdUser()
    {
        return $this->belongsTo('\App\User', 'created_user_id');
    }

    /**
     * User who last modified record
     * @return \App\User
     */
    public function modifiedUser()
    {
        return $this->belongsTo('\App\User', 'modified_user_id');
    }
}
