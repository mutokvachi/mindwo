<?php

namespace App\Models\Crypto;

use Illuminate\Database\Eloquent\Model;

/**
 * User's master key
 */
class Masterkey extends Model
{

    /**
     * @var string Related table
     */
    protected $table = 'dx_crypto_masterkeys';

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
     * Master key's owner
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
