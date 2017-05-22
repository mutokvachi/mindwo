<?php

namespace App\Models\Crypto;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for crypto master key regenration processes
 */
class Regen extends Model
{
 /**
     * @var string Related table
     */
    protected $table = 'dx_crypto_regen';

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
    
    public function cache()
    {
        return $this->hasMany('\App\Models\Crypto\Cache', 'regen_id');
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
