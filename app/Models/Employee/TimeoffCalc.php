<?php

namespace App\Models\Employee;

use Illuminate\Database\Eloquent\Model;

/**
 * Time off calculation's model 
 */
class TimeoffCalc extends Model
{
    /**
     * @var string Related table
     */
    protected $table = 'dx_timeoff_calc';

    /**
     * @var bool Disables Laravel's time stamps on insert and update
     */
    public $timestamps = false;
}
