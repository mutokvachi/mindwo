<?php

namespace App\Models\Education;

use Illuminate\Database\Eloquent\Model;

class OrgUser extends Model
{
     /**
     * @var string Related table
     */
    protected $table = 'edu_orgs_users';

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
     * oragnization where use works
     *
     * @return void
     */
    public function organization()
    {
        return $this->belongsTo('\App\Models\Education\Organization', 'org_id');
    }

    /**
     * User card
     * @return \App\User
     */
    public function user()
    {
        return $this->belongsTo('\App\User', 'user_id');
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
