<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelis priekš datu bāzes tabulas 'in_employees_history'
 */
class EmployeesHistory extends Model
{
    /**
     * @var string Saistītā tabula
     */
    protected $table = 'in_employees_history';

    /**
     * @var bool Atslēdz laravel iebūvēto modeļu izveides un labošanas laiku uzskaiti
     */
    public $timestamps = false;  
}
