<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Lotus Notes dokumentu tipu modelis
 */
class DocumentKind extends Model
{
    /**
     * @var string Saistītā tabula
     */
    protected $table = 'in_documents_kind';

    /**
     * @var bool Atslēdz laravel iebūvēto modeļu izveides un labošanas laiku uzskaiti
     */
    public $timestamps = false;
}
