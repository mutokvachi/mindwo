<?php

namespace App\Models\Workflow;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for worflow task types
 */
class TaskType extends Model
{
    /**
     * @var string Saistītā tabula
     */
    protected $table = 'dx_tasks_types';

    /**
     * @var bool Atslēdz laravel iebūvēto modeļu izveides un labošanas laiku uzskaiti
     */
    public $timestamps = false;
}
