<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Lotus Notes dokumentu modelis
 */
class Document extends Model
{
    /**
     * @var string Saistītā tabula
     */
    protected $table = 'in_documents';

    /**
     * @var bool Atslēdz laravel iebūvēto modeļu izveides un labošanas laiku uzskaiti
     */
    public $timestamps = false;

    /**
     * Sasaiste ar dokumentu tipu
     * 
     * @return App\Models\DocumentKind atbilstošais dokumenta tips
     */
    public function documentKind()
    {
        return $this->belongsTo('App\Models\DocumentKind', 'doc_kind_id');
    }

    /**
     * Sasaiste ar dokumentu nodaļu
     * 
     * @return App\Models\DocumentDepartment atbilstošā dokumenta nodaļa
     */
    public function documentDepartment()
    {
        return $this->belongsTo('App\Models\DocumentDepartment', 'doc_department_id');
    }
}