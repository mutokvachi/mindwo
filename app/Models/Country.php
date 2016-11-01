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
    public function employeePersonalDocs()
    {
        return $this->hasMany('\App\Models\Employee\PersonalDocument', 'country_id');
    }
}
