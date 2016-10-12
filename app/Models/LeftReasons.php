<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelis priekš datu bāzes tabulas 'in_left_reasons'
 */
class LeftReasons extends Model
{
    /**
     * @var string Saistītā tabula
     */
    protected $table = 'in_left_reasons';

    /**
     * @var bool Atslēdz laravel iebūvēto modeļu izveides un labošanas laiku uzskaiti
     */
    public $timestamps = false;  
}
