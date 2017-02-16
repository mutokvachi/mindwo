<?php

namespace App\Models\Calendar;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for months classifier
 */
class Month extends Model
{
    /**
     * @var string Related table
     */
    protected $table = 'dx_months';

    /**
     * @var bool Disables Laravel's time stamps on insert and update
     */
    public $timestamps = false;
}
