<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

/**
 * Model for available system roles
 */
class Role extends Model
{
     /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'dx_roles';
    
    /**
     * Changes default column name for column updated_at 
     */
    const UPDATED_AT = 'modified_time';
    
    /**
     * Changes default column name for column created_at 
     */
    const CREATED_AT = 'created_time';
}
