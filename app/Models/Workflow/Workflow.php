<?php

namespace App\Models\Workflow;

use Illuminate\Database\Eloquent\Model;

class Workflow extends Model
{
    /**
     * @var string Saistītā tabula
     */
    protected $table = 'dx_workflows_def';

    /**
     * @var bool Atslēdz laravel iebūvēto modeļu izveides un labošanas laiku uzskaiti
     */
    public $timestamps = false;

    public function workflowSteps()
    {
        return $this->hasMany('\App\Models\Workflow\WorkflowStep', 'workflow_def_id');
    }
}