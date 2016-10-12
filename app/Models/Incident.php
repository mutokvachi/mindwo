<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelis priekš datu bāzes tabulas 'in_incidents'
 */
class Incident extends Model
{
    /**
     * @var string Saistītā tabula
     */
    protected $table = 'in_incidents';

    /**
     * @var bool Atslēdz laravel iebūvēto modeļu izveides un labošanas laiku uzskaiti
     */
    public $timestamps = false;
    
    /**
     * Sasaiste ar sistēmu
     * 
     * @return App\Models\System atbilstošā sistēma
     */
    public function system()
    {
        return $this->belongsTo('App\Models\System');
    }
}