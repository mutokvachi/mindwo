<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelis priekš datu bāzes tabulas 'in_systems'
 */
class System extends Model
{
    /**
     * @var string Saistītā tabula
     */
    protected $table = 'in_systems';

    /**
     * @var bool Atslēdz laravel iebūvēto modeļu izveides un labošanas laiku uzskaiti
     */
    public $timestamps = false;
}