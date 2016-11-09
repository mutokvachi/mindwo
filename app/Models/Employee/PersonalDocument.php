<?php

namespace App\Models\Employee;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for employee's personal documents
 */
class PersonalDocument extends Model
{
    /**
     * @var string Related table
     */
    protected $table = 'in_personal_docs';

    /**
     * @var bool Disables Laravel's time stamps on insert and update
     */
    public $timestamps = false;
}
