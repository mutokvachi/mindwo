<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    /**
     * @var string Related table
     */
    protected $table = 'dx_chats';

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
     * Certificate's owner
     * @return App\User
     */
    public function messages()
    {
        return $this->hasMany('\App\Models\Chat\Message', 'chat_id');
    }

    /**
     * Chat's list
     *
     * @return \App\Models\Lists
     */
    public function list()
    {
        return $this->belongsTo('\App\Models\Lists', 'list_id');
    }

    /**
     * Chat's participant
     * @return App\User
     */
    public function users()
    {
        return $this->belongsToMany('\App\User', 'dx_chats_users', 'chat_id', 'user_id');
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
