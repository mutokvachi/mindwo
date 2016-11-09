<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for countries classiifiers
 */
class Country extends Model
{

    /**
     * @var string Related table
     */
    protected $table = 'dx_countries';

    /**
     * @var bool Disables Laravel's time stamps on insert and update
     */
    public $timestamps = false;

    /**
     * List of employee's personal documents in given country
     * @return App\Employee\PersonalDocument
     */
    public function personalDocs()
    {
        return $this->belongsToMany('\App\Models\Employee\PersonalDocument', 'in_personal_docs_countries', 'country_id', 'doc_id');
    }

    /**
     * Gets flag image
     * @return string Url of the flag's image
     */
    public function getFlag()
    {
        if ($this->flag_file_guid && is_file(public_path("img/{$this->flag_file_guid}"))) {
            return url("img/{$this->flag_file_guid}");
        }

        return '';
    }
}
