<?php

namespace App\Models\Employee;

use Illuminate\Database\Eloquent\Model;

// Employee's accrual policy model
class UserAccrualPolicy extends Model
{
    /**
     * @var string Related table
     */
    protected $table = 'dx_users_accrual_policies';

    /**
     * @var bool Disables Laravel's time stamps on insert and update
     */
    public $timestamps = false;
    
    /**
     * Time off type for policy
     * @return \App\Models\Employee\TimeoffType
     */
    public function timeoffType()
    {
        return $this->belongsTo('\App\Models\Employee\TimeoffType', 'timeoff_type_id');
    }
    
    
}
