<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelis priekš datu bāzes tabulas 'in_processes'
 */
class Process extends Model
{
    /**
     * @var string Saistītā tabula
     */
    protected $table = 'in_processes';

    /**
     * @var bool Atslēdz laravel iebūvēto modeļu izveides un labošanas laiku uzskaiti
     */
    public $timestamps = false;

    /**
     * Iegūst saistītos procesa audita ierakstus
     * 
     * @return App\ProcessLog Saraksts ar procesa audita ierakstiem
     */
    public function processLogs()
    {
        return $this->hasMany('App\ProcessLog');
    }
    
    /**
     * Sasaiste ar atbildīgo darbinieku
     * 
     * @return App\Models\Employee atbildīgais darbinieks
     */
    public function employee()
    {
        return $this->belongsTo('App\Models\Employee');
    }
}