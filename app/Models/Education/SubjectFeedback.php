<?php

namespace App\Models\Education;

use Illuminate\Database\Eloquent\Model;

class SubjectFeedback extends Model
{
     /**
     * @var string Related table
     */
    protected $table = 'edu_subjects_feedbacks';

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
