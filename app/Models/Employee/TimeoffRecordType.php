<?php

namespace App\Models\Employee;

use Illuminate\Database\Eloquent\Model;

/**
 * Time off record's type model
 */
class TimeoffRecordType extends Model
{
    /**
     * @var string Related table
     */
    protected $table = 'dx_timeoff_records_types';

    /**
     * @var bool Disables Laravel's time stamps on insert and update
     */
    public $timestamps = false;
}
