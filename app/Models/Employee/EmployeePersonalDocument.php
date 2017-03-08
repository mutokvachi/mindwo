<?php

namespace App\Models\Employee;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for employee's personal documents
 */
class EmployeePersonalDocument extends Model
{
    /**
     * @var string Related table
     */
    protected $table = 'in_employees_personal_docs';

    /**
     * @var bool Disables Laravel's time stamps on insert and update
     */
    public $timestamps = false;
    
    /**
     * Gets related personal document type
     * @return \App\Models\Employee\PersonalDocument
     */
    public function personalDocument()
    {
        return $this->belongsTo('\App\Models\Employee\PersonalDocument', 'doc_id', 'id');
    }
}
