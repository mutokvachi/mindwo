<?php

namespace App\Models\Employee;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for employee's comments tab
 */
class Note extends Model
{

    /**
     * @var string Related table
     */
    protected $table = 'in_employees_notes';

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
     * User who last created note
     * @return \App\User
     */
    public function createdUser()
    {
        return $this->belongsTo('\App\User', 'created_user_id');
    }
    
    /**
     * User who last modified note
     * @return \App\User
     */
    public function modifiedUser()
    {
        return $this->belongsTo('\App\User', 'modified_user_id');
    }
}
