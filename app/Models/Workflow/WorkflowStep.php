<?php

namespace App\Models\Workflow;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for workflow step object
 */
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
    
    /**
     * Worflows steps task type
     * @return \App\Models\Workflow\TaskType
     */
    public function taskType()
    {
        return $this->belongsTo('\App\Models\Workflow\TaskType', 'task_type_id');
    }
}
