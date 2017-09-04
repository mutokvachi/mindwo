<?php

namespace App\Models\Calendar;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for months classifier
 */
class Month extends Model
{

    protected $fillable = [
        'title',
        'short_title',
        'nr',
        'created_user_id',
        'created_time',
        'modified_user_id',
        'modified_time',
    ];

    /**
     * @var string Related table
     */
    protected $table = 'dx_months';

    /**
     * @var bool Disables Laravel's time stamps on insert and update
     */
    public $timestamps = false;
}
