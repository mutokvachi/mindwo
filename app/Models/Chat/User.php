<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
/**
     * @var string Related table
     */
    protected $table = 'dx_chats_users';

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
     * Chat's participant
     * @return App\User
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
