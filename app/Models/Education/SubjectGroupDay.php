<?php

namespace App\Models\Education;

use Illuminate\Database\Eloquent\Model;

class SubjectGroupDay extends Model
{
     /**
     * @var string Related table
     */
    protected $table = 'edu_subjects_groups_days';

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
        'modified_time',
        'lesson_date'
    ];

    /**
     * Specified room
     *
     * @return \App\Models\Education\Room
     */
    public function room()
    {
        return $this->belongsTo('\App\Models\Education\Room', 'room_id');
    }

    /**
     * All teachers at specified day
     *
     * @return \App\User
     */
    public function teachers()
    {
        return $this->belongsToMany('\App\User', 'edu_subjects_groups_days_teachers', 'group_day_id', 'teacher_id');
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
