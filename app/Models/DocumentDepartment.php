<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Lotus Notes dokumentu departamenti
 */
class DocumentDepartment extends Model
{
    /**
     * @var string Saistītā tabula
     */
    protected $table = 'in_doc_departments';

    /**
     * @var bool Atslēdz laravel iebūvēto modeļu izveides un labošanas laiku uzskaiti
     */
    public $timestamps = false;
}
