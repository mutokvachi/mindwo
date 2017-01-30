<?php

namespace App\Models\Workflow;

use Illuminate\Database\Eloquent\Model;

class WorkflowStep extends Model
{
    /**
     * @var string Saistītā tabula
     */
    protected $table = 'dx_workflows';

    /**
     * @var bool Atslēdz laravel iebūvēto modeļu izveides un labošanas laiku uzskaiti
     */
    public $timestamps = false;
}
