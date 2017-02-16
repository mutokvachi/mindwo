<?php

namespace App\Models\Calendar;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for days classifier
 */
class Day extends Model
{
    /**
     * @var string Related table
     */
    protected $table = 'dx_month_days';

    /**
     * @var bool Disables Laravel's time stamps on insert and update
     */
    public $timestamps = false;
}
