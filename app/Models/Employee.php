<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelis priekš datu bāzes tabulas 'in_employees'
 */
class Employee extends Model
{
    /**
     * @var string Saistītā tabula
     */
    protected $table = 'in_employees';

    /**
     * @var bool Atslēdz laravel iebūvēto modeļu izveides un labošanas laiku uzskaiti
     */
    public $timestamps = false;

    /**
     * Sasaiste ar departmentu
     * 
     * @return App\Models\Department atbilstošais departments
     */
    public function department()
    {
        return $this->belongsTo('App\Models\Department');
    }
    
    /**
     * List of employee's personal documents
     * @return App\Employee\EmployeePersonalDocument
     */
    public function employeePersonalDocs()
    {        
        return $this->hasMany('\App\Models\Employee\EmployeePersonalDocument', 'employee_id');
    }
}