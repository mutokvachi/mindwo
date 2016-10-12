<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelis priekš datu bāzes tabulas 'in_processes_log'
 */
class ProcessLog extends Model
{
    /**
     * @var string Saistītā tabula
     */
    protected $table = 'in_processes_log';

    /**
     * @var bool Atslēdz laravel iebūvēto modeļu izveides un labošanas laiku uzskaiti
     */
    public $timestamps = false;

    /**
     * Sasaiste ar procesu moduli
     * 
     * @return App\ProcessLog Saraksts ar procesa audita ierakstiem
     */
    public function process()
    {
        return $this->belongsTo('App\Process');
    }
}