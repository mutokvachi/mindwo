<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Sistēmas, no kurām izgūst datus un pieslēguma url
 */
class DocumentsLotus extends Model
{
    /**
     * @var string Saistītā tabula
     */
    protected $table = 'in_documents_lotus';

    /**
     * @var bool Atslēdz laravel iebūvēto modeļu izveides un labošanas laiku uzskaiti
     */
    public $timestamps = false;
}
