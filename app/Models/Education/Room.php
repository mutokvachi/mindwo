<?php

namespace App\Models\Education;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;

class Room extends Model
{
     /**
     * @var string Related table
     */
    protected $table = 'edu_rooms';

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

    public function organization()
    {
        return $this->belongsTo('\App\Models\Education\Organization', 'org_id');
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
