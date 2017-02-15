<?php

namespace App\Models\Calendar;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for holidays which are registered in system
 */
class Holiday extends Model
{
    /**
     * @var string Related table
     */
    protected $table = 'dx_holidays';

    /**
     * @var bool Disables Laravel's time stamps on insert and update
     */
    public $timestamps = false;
    
     public function createdUser()
    {
        return $this->belongsTo('\App\User', 'created_user_id');
    }
}
