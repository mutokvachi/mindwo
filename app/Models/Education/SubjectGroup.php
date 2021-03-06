<?php

namespace App\Models\Education;

use Illuminate\Database\Eloquent\Model;

class SubjectGroup extends Model
{
     /**
     * @var string Related table
     */
    protected $table = 'edu_subjects_groups';

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
     * Related subject
     *
     * @return \App\Models\Education\Subject
     */
    public function subject()
    {
        return $this->belongsTo('\App\Models\Education\Subject', 'subject_id');
    }

    public function days()
    {
        return $this->hasMany('\App\Models\Education\SubjectGroupDay', 'group_id');
    }

    public function members()
    {
        return $this->hasMany('\App\Models\Education\SubjectGroupMember', 'group_id');
    }

    public function organization()
    {
        return $this->belongsTo('\App\Models\Education\Organization', 'inner_org_id');
    }

    public function firstDay()
    {
        return $this->hasMany('\App\Models\Education\SubjectGroupDay', 'group_id')
            ->orderBy('lesson_date', 'ASC')->first();
    }

    public function lastDay()
    {
        return $this->hasMany('\App\Models\Education\SubjectGroupDay', 'group_id')
            ->orderBy('lesson_date', 'DESC')->first();
    }

     public function dateInterval()
    {
        $groupStartDay = $this->firstDay();
        if ($groupStartDay) {
            $groupStartDate = date_create($groupStartDay->lesson_date)->format('d.m.Y');
        } else {
            $groupStartDate = '';
        }

        $groupEndDay = $this->lastDay();
        if ($groupEndDay) {
            $groupEndDate = date_create($groupStartDay->lesson_date)->format('d.m.Y');
        } else {
            $groupEndDate = '';
        }

        $dateInterval = '';

        if(strlen($groupStartDate)>0){
            $dateInterval .= '(' . $groupStartDate;

            if(strlen($groupEndDate) > 0 && $groupEndDate != $groupStartDate){
                $dateInterval .= ' - ' . $groupEndDate . ')';
            } else {
                 $dateInterval .= ')';
            }
        }
        return $dateInterval;
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
